<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use OpenAI\Laravel\Facades\OpenAI;

class PromptGeneratorController extends Controller
{
    /**
     * gpt-4o-mini specifically - fast and inexpensive, which matters
     * here since every single generation is a live, uncached API call
     * (nothing is ever stored or reused).
     */
    protected const MODEL = 'gpt-4o-mini';

    /**
     * Higher than a factual/deterministic task would use - natural,
     * varied phrasing matters more here than precision, since the
     * output is itself creative writing (a prompt), not a fact lookup.
     */
    protected const TEMPERATURE = 0.8;

    /**
     * Reads the flashed result/error from the redirect below - both are
     * null on a plain first visit, which the view already treats as the
     * empty state.
     */
    public function index(): View
    {
        return view('prompt-generator', [
            'result' => session('result'),
            'error' => session('error'),
        ]);
    }

    /**
     * Post/Redirect/Get - this always redirects back to GET / rather
     * than returning a view directly from the POST handler. That's what
     * actually fixes the browser's "Confirm Form Resubmission" warning:
     * with a view returned directly from POST, the browser's history
     * entry for that page IS a POST, so any refresh (or back/forward
     * navigation) has to ask before resending it. After a redirect, the
     * last real request in history is a plain GET, so refreshing just
     * re-requests that - no warning, ever.
     *
     * withInput() flashes $data the exact same way Laravel's own
     * validate() already does on a validation failure - the view's
     * old('topic') calls work identically whether this request failed
     * validation, failed calling OpenAI, or succeeded, since all three
     * paths now flash input the same way.
     */
    public function generate(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'topic' => 'required|string|max:300',
            'content_type' => 'required|string|max:100',
            'tone' => 'required|string|max:100',
            'audience' => 'nullable|string|max:200',
            'key_points' => 'nullable|string|max:1000',
            'length' => 'required|string|max:50',
            'instructions' => 'nullable|string|max:1000',
        ]);

        try {
            $payload = [
                'model' => self::MODEL,
                'temperature' => self::TEMPERATURE,
                'messages' => [
                    ['role' => 'system', 'content' => $this->systemPrompt()],
                    ['role' => 'user', 'content' => $this->userMessage($data)],
                ],
            ];

            $response = OpenAI::chat()->create($payload);

            $result = trim($response->choices[0]->message->content ?? '');

            if ($result === '') {
                throw new \RuntimeException('Empty response from OpenAI.');
            }
        } catch (\Throwable $e) {
            report($e);

            return redirect('/')
                ->withInput($data)
                ->with('error', 'Something went wrong generating your prompt. Please try again in a moment.');
        }

        return redirect('/')
            ->withInput($data)
            ->with('result', $result);
    }

    /**
     * The whole point of this tool: the model writes a PROMPT someone
     * can paste into any AI writing tool - it never writes the actual
     * content itself. No Google Helpful Content guideline framing here
     * at all, unlike PublishFit's own tools - this is a general-purpose
     * prompt writer, on purpose, for now.
     */
    protected function systemPrompt(): string
    {
        return <<<'PROMPT'
            You are an expert prompt engineer. Your job is to take a set of
            content requirements and turn them into a single, clear,
            ready-to-use prompt that someone can paste directly into an AI
            writing tool (like ChatGPT or Claude) to get exactly the content
            they described.

            Write ONLY the prompt itself. Do not write the actual content.
            Do not add any preamble, explanation, labels, or commentary
            before or after the prompt - output the prompt text and nothing
            else.
            PROMPT;
    }

    /**
     * @param  array<string, string|null>  $data
     */
    protected function userMessage(array $data): string
    {
        $lines = [
            "Content Type: {$data['content_type']}",
            "Topic: {$data['topic']}",
            "Tone: {$data['tone']}",
            "Desired Length: {$data['length']}",
        ];

        if (! empty($data['audience'])) {
            $lines[] = "Target Audience: {$data['audience']}";
        }

        if (! empty($data['key_points'])) {
            $lines[] = "Key Points to Include: {$data['key_points']}";
        }

        if (! empty($data['instructions'])) {
            $lines[] = "Additional Instructions: {$data['instructions']}";
        }

        $lines[] = "\nWrite the ready-to-use content-generation prompt now.";

        return implode("\n", $lines);
    }
}