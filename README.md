# PromptForge

**Repository: `publishfit-prompt-generator`** — a free, open-source, single-page AI content prompt generator built with Laravel 13.

Describe what you need — topic, content type, tone, audience, and a few other details — and PromptForge writes a clear, ready-to-use prompt you can paste directly into ChatGPT, Claude, or any other AI writing tool to get exactly the content you're after.

PromptForge doesn't write the content itself — it writes the **prompt** that gets you there. Nothing is saved, no accounts, no tracking. Every generation is a live, one-off call to OpenAI.

## Features

- One page, one form, no sign-up
- Structured inputs (content type, tone, audience, key points, length, extra instructions) so the generated prompt is genuinely well-formed, not just a vague restatement of what you typed
- Powered by OpenAI's `gpt-4o-mini`
- Nothing stored anywhere — no database writes, no sessions beyond what Laravel needs for CSRF/validation
- MIT licensed — fork it, extend it, use it commercially, whatever you like

## Requirements

- PHP 8.2 or higher
- Composer
- An [OpenAI API key](https://platform.openai.com/api-keys)

## Installation

1. **Clone the repository**

    By default, `git clone` creates a folder using the repository's own name:

    ```bash
    git clone https://github.com/jiten14/publishfit-prompt-generator.git
    cd publishfit-prompt-generator
    ```

    If you'd rather use your own folder name, add it as an extra argument after the URL - for example, to clone it into a folder called `my-prompt-tool` instead:

    ```bash
    git clone https://github.com/jiten14/publishfit-prompt-generator.git my-prompt-tool
    cd my-prompt-tool
    ```

    Either way works identically from here on - just make sure the folder name in your `cd` command matches whichever name you actually cloned it into.

2. **Install dependencies**

    ```bash
    composer install
    ```

3. **Set up your environment file**

    ```bash
    cp .env.example .env
    php artisan key:generate
    ```

4. **Add your OpenAI API key**

    This repo already includes `config/openai.php` (generated once via `php artisan openai:install` and committed) - you don't need to run that command yourself. Just open your `.env` file and fill in:

    ```env
    OPENAI_API_KEY=sk-your-actual-key-here
    OPENAI_ORGANIZATION=
    ```

    `OPENAI_API_KEY` is the only one you actually need to fill in for most accounts. `OPENAI_ORGANIZATION` can stay blank unless your OpenAI account specifically requires an organization ID - see the [official openai-php/laravel docs](https://github.com/openai-php/laravel) if you're not sure.

5. **(Optional) Public demo mode**

    Running this somewhere the general public can access it (like a free Render demo) with your own `OPENAI_API_KEY` set means every visitor's generation is billed to *your* account - with no login and no rate limiting, that's an open-ended cost risk.

    Set this one extra variable to flip the app into demo mode instead:

    ```env
    DEMO_MODE=true
    ```

    When it's set, every visitor is asked for their **own** OpenAI API key before generating anything - it's used for that single request only and is never stored, logged, or saved anywhere (not a database, not a cookie, not a session). Your own `OPENAI_API_KEY` becomes entirely unused for actual generation once this is on, so you can safely leave it blank on a public demo deployment specifically.

    Leave `DEMO_MODE` unset (the default) for your own private use, and the app behaves exactly as it did before - your `.env` key is used directly, with no prompt shown at all.

6. **Run it**

    ```bash
    php artisan serve
    ```

    Visit `http://localhost:8000` and you're generating prompts.

## How it works

Want to see it running before you set anything up? **[Try the live demo](https://publishfit-prompt-generator.onrender.com/)** — it's in demo mode, so you'll need your own OpenAI API key to generate a prompt (see [Public demo mode](#installation) above for why).

This demo is hosted on [Render](https://render.com)'s free plan, which means the instance spins down after periods of inactivity. If nobody has visited recently, your first request may take 30–60 seconds to load while it spins back up — completely normal for a free-tier deployment, and everything runs smoothly afterward.

- `GET /` shows the form.
- `POST /` validates your input, sends it to OpenAI with a system prompt instructing the model to write a *prompt*, not the content itself, and renders the result on the same page next to your original input.
- Nothing is persisted — refresh the page and it's a clean slate.

All of the actual logic lives in one controller: `app/Http/Controllers/PromptGeneratorController.php`. If you want to extend this (more content types, different output formats, saving history, adding auth), that's the file to start from.

## Roadmap

This is intentionally minimal for now — just content prompts, no guideline-following, no saved history. Planned next: additional prompt types beyond general content writing (this is being expanded gradually).

## A related tool worth knowing about

If you're generating content and want to make sure it actually holds up against Google's Helpful Content guidelines before you publish — not just that the prompt was good — check out **[PublishFit](https://publishfit.com)**. It scores your content, rewrites the parts that fall short, and generates guideline-aligned prompts of its own. PromptForge and PublishFit aren't the same tool (this one is deliberately general-purpose), but if you're already thinking about prompts, PublishFit is the natural next step before you hit publish.

## License

MIT — see [LICENSE](LICENSE) for details. Use it, fork it, build on it.