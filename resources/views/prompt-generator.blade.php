<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>PromptForge - AI Content Prompt Generator</title>
    <meta name="description" content="Turn a few details into a ready-to-use, well-structured AI content prompt in seconds.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@600;700&family=Inter:wght@400;500;600&family=IBM+Plex+Mono:wght@400;500&display=swap" rel="stylesheet">
    <style>
        *,::after,::before{box-sizing:border-box;margin:0;padding:0}

        :root{
            --indigo:#4F46E5;
            --indigo-deep:#3730A3;
            --amber:#F59E0B;
            --ink:#1E1B2E;
            --gray:#6B7280;
            --paper:#FFFFFF;
            --bg:#F5F5FB;
            --border:#E3E1F5;
        }

        html{scroll-behavior:smooth}
        body{
            font-family:'Inter',Arial,Helvetica,sans-serif;background-color:var(--bg);color:var(--ink);
            line-height:1.55;min-height:100vh;display:flex;flex-direction:column;
        }

        /* ============ Header ============ */
        .site-header{background-color:var(--paper);border-bottom:1px solid var(--border);padding:1.1rem 2rem}
        .logo{
            font-family:'Space Grotesk',sans-serif;font-weight:700;font-size:1.3rem;text-decoration: none;
            background:linear-gradient(135deg,var(--indigo),var(--indigo-deep));
            -webkit-background-clip:text;background-clip:text;color:transparent;letter-spacing:-0.01em;
        }

        /* ============ Content ============ */
        .content{flex:1;padding:3rem 2rem}
        .wrap{max-width:80rem;margin:0 auto}

        .hero{max-width:38rem}
        .hero h1{font-family:'Space Grotesk',sans-serif;font-weight:700;font-size:2rem;color:var(--ink);letter-spacing:-0.02em}
        .hero p{margin-top:0.6rem;color:var(--gray);font-size:1rem}

        .layout{margin-top:2rem;display:grid;grid-template-columns:1fr 1fr;gap:1.75rem;align-items:start}
        @media (max-width:860px){.layout{grid-template-columns:1fr}}

        .card{background-color:var(--paper);border:1px solid var(--border);border-radius:16px;padding:2rem;box-shadow:0 4px 20px rgba(79,70,229,0.06)}
        .card-title{font-family:'Space Grotesk',sans-serif;font-weight:700;font-size:1.15rem;color:var(--ink)}

        .field{margin-top:1.25rem}
        .field:first-of-type{margin-top:1.5rem}
        label{display:block;font-weight:600;font-size:0.875rem;color:var(--ink)}
        .req{color:var(--amber);font-weight:700}
        .opt{color:#A1A1B5;font-weight:400;font-size:0.78rem}
        .field-hint{margin-top:0.3rem;font-size:0.78rem;color:var(--gray)}

        input[type=text], textarea, select{
            margin-top:0.4rem;width:100%;padding:0.7rem 0.85rem;border:1px solid var(--border);
            border-radius:10px;font-size:0.92rem;font-family:'Inter',sans-serif;color:var(--ink);
            background-color:var(--paper);
        }
        textarea{resize:vertical;min-height:4.5rem}
        input:focus, textarea:focus, select:focus{
            outline:none;border-color:var(--indigo);box-shadow:0 0 0 3px rgba(79,70,229,0.14);
        }

        .row-2{display:grid;grid-template-columns:1fr 1fr;gap:1rem}
        @media (max-width:480px){.row-2{grid-template-columns:1fr}}

        .btn-row{margin-top:1.75rem;display:flex;gap:0.75rem}
        .generate-btn{
            flex:1;background:linear-gradient(135deg,var(--indigo),var(--indigo-deep));
            color:#fff;border:none;border-radius:999px;padding:0.85rem 1.5rem;font-size:0.98rem;font-weight:700;
            cursor:pointer;font-family:'Inter',sans-serif;transition:opacity .15s ease,transform .15s ease;
        }
        .generate-btn:hover{opacity:0.92;transform:translateY(-1px)}
        .generate-btn:disabled{opacity:0.6;cursor:not-allowed;transform:none}

        .clear-btn{
            flex-shrink:0;background:none;border:1px solid var(--border);color:var(--gray);
            border-radius:999px;padding:0.85rem 1.5rem;font-size:0.92rem;font-weight:600;
            cursor:pointer;font-family:'Inter',sans-serif;transition:background-color .15s ease,color .15s ease;
        }
        .clear-btn:hover{background-color:#EEEDFB;color:var(--indigo-deep)}

        /* ============ Output ============ */
        .output-empty{
            display:flex;flex-direction:column;align-items:center;justify-content:center;text-align:center;
            min-height:20rem;color:#A1A1B5;
        }
        .output-empty-icon{
            width:3rem;height:3rem;border-radius:999px;background-color:#EEEDFB;color:var(--indigo);
            display:flex;align-items:center;justify-content:center;margin-bottom:1rem;
        }
        .output-empty p{max-width:20rem;font-size:0.9rem}

        .error-box{
            background-color:#FEF2F2;border:1px solid #FCA5A5;color:#B91C1C;border-radius:10px;
            padding:0.9rem 1.1rem;font-size:0.875rem;margin-bottom:1.25rem;
        }

        .output-box{
            background-color:#1E1B2E;border-radius:12px;padding:1.5rem;color:#EDEBFF;
            font-family:'IBM Plex Mono',monospace;font-size:0.875rem;line-height:1.7;
            white-space:pre-wrap;word-break:break-word;max-height:26rem;overflow-y:auto;
        }

        .copy-row{margin-top:1.1rem;display:flex;align-items:center;gap:0.85rem}
        .copy-btn{
            display:inline-flex;align-items:center;gap:0.5rem;background-color:var(--amber);color:#1E1B2E;
            border:none;border-radius:999px;padding:0.65rem 1.4rem;font-size:0.88rem;font-weight:700;
            cursor:pointer;font-family:'Inter',sans-serif;transition:opacity .15s ease;
        }
        .copy-btn:hover{opacity:0.88}
        .copied-note{color:var(--indigo-deep);font-size:0.85rem;font-weight:600;opacity:0;transition:opacity .15s ease}
        .copied-note.show{opacity:1}

        /* ============ Footer ============ */
        .site-footer{padding:1.5rem 2rem;text-align:center;color:#A1A1B5;font-size:0.8rem}
    </style>
</head>
<body>
    <header class="site-header">
        <a href="/" class="logo">PromptForge</a>
    </header>

    <div class="content">
        <div class="wrap">
            <div class="hero">
                <h1>Turn a few details into the perfect AI prompt</h1>
                <p>Describe what you need, and PromptForge writes a clear, ready-to-use prompt you can paste straight into ChatGPT, Claude, or any AI writing tool.</p>
            </div>

            <div class="layout">
                <div class="card">
                    <div class="card-title">Describe what you need</div>

                    <form method="POST" action="/" id="prompt-form">
                        @csrf

                        <div class="field">
                            <label for="topic">Topic <span class="req">*</span></label>
                            <input type="text" id="topic" name="topic" value="{{ old('topic') }}" placeholder="e.g. Benefits of cold showers">
                            @error('topic') <p class="field-hint" style="color:#DC2626">{{ $message }}</p> @enderror
                        </div>

                        <div class="field row-2">
                            <div>
                                <label for="content_type">Content Type <span class="req">*</span></label>
                                <select id="content_type" name="content_type">
                                    @php $contentType = old('content_type', ''); @endphp
                                    <option value="" disabled {{ $contentType === '' ? 'selected' : '' }}>Select type...</option>
                                    @foreach (['Blog Post', 'Social Media Caption', 'Product Description', 'Email', 'Video Script', 'Ad Copy', 'Landing Page Copy'] as $type)
                                        <option value="{{ $type }}" {{ $contentType === $type ? 'selected' : '' }}>{{ $type }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="tone">Tone <span class="req">*</span></label>
                                <select id="tone" name="tone">
                                    @php $tone = old('tone', ''); @endphp
                                    <option value="" disabled {{ $tone === '' ? 'selected' : '' }}>Select tone...</option>
                                    @foreach (['Professional', 'Casual', 'Persuasive', 'Humorous', 'Authoritative', 'Empathetic'] as $t)
                                        <option value="{{ $t }}" {{ $tone === $t ? 'selected' : '' }}>{{ $t }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="field">
                            <label for="audience">Target Audience <span class="opt">(optional)</span></label>
                            <input type="text" id="audience" name="audience" value="{{ old('audience') }}" placeholder="e.g. busy new parents">
                        </div>

                        <div class="field">
                            <label for="key_points">Key Points to Include <span class="opt">(optional)</span></label>
                            <textarea id="key_points" name="key_points" placeholder="One per line or comma-separated">{{ old('key_points') }}</textarea>
                        </div>

                        <div class="field">
                            <label for="length">Desired Length <span class="req">*</span></label>
                            <select id="length" name="length">
                                @php $length = old('length', ''); @endphp
                                <option value="" disabled {{ $length === '' ? 'selected' : '' }}>Select length...</option>
                                @foreach (['Short', 'Medium', 'Long'] as $l)
                                    <option value="{{ $l }}" {{ $length === $l ? 'selected' : '' }}>{{ $l }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="field">
                            <label for="instructions">Additional Instructions <span class="opt">(optional)</span></label>
                            <textarea id="instructions" name="instructions" placeholder="Anything else the prompt should account for">{{ old('instructions') }}</textarea>
                        </div>

                        <div class="btn-row">
                            <button type="submit" class="generate-btn" id="generate-btn">Generate Prompt</button>
                            <button type="button" class="clear-btn" id="clear-btn">Clear</button>
                        </div>
                    </form>
                </div>

                <div class="card">
                    <div class="card-title">Your generated prompt</div>

                    <div id="output-content">
                        @if ($error)
                            <div class="error-box" style="margin-top:1.25rem">{{ $error }}</div>
                        @endif

                        @if ($result)
                            <div class="output-box" style="margin-top:1.25rem">{{ $result }}</div>
                            <div class="copy-row">
                                <button type="button" class="copy-btn" id="copy-btn">Copy Prompt</button>
                                <span class="copied-note" id="copied-note">Copied!</span>
                            </div>
                        @elseif (! $error)
                            <div class="output-empty">
                                <div class="output-empty-icon">
                                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none"><path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                                </div>
                                <p>Fill in the form and click Generate - your ready-to-use prompt will appear here.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="site-footer">
        &copy; {{ now()->year }} PromptForge
    </footer>

    <script>
        (function () {
            const form = document.getElementById('prompt-form');
            const generateBtn = document.getElementById('generate-btn');
            const clearBtn = document.getElementById('clear-btn');
            const copyBtn = document.getElementById('copy-btn');
            const copiedNote = document.getElementById('copied-note');

            if (form && generateBtn) {
                form.addEventListener('submit', () => {
                    generateBtn.disabled = true;
                    generateBtn.textContent = 'Generating...';
                });
            }

            if (clearBtn && form) {
                clearBtn.addEventListener('click', () => {
                    form.querySelectorAll('input[type="text"], textarea').forEach((el) => { el.value = ''; });
                    form.querySelectorAll('select').forEach((el) => { el.selectedIndex = 0; });

                    const outputContent = document.getElementById('output-content');
                    if (outputContent) {
                        outputContent.innerHTML = `
                            <div class="output-empty">
                                <div class="output-empty-icon">
                                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none"><path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                                </div>
                                <p>Fill in the form and click Generate - your ready-to-use prompt will appear here.</p>
                            </div>
                        `;
                    }
                });
            }

            if (copyBtn) {
                copyBtn.addEventListener('click', () => {
                    const text = document.querySelector('.output-box').innerText;
                    navigator.clipboard.writeText(text).then(() => {
                        copiedNote.classList.add('show');
                        setTimeout(() => copiedNote.classList.remove('show'), 1800);
                    });
                });
            }
        })();
    </script>
</body>
</html>