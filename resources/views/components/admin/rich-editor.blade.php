@props([
    'name',
    'value' => '',
    'dir' => 'ltr',
])

@php
    $editorId = 'editor-' . $name;
@endphp

<div class="rich-editor mb-3" data-rich-editor data-target="{{ $editorId }}">
    <div class="rich-editor__toolbar d-flex flex-wrap gap-1 p-2 border rounded-top bg-light">
        <button type="button" class="re-btn" data-cmd="bold" title="@lang('admin.Bold')"><i class="bi bi-type-bold"></i></button>
        <button type="button" class="re-btn" data-cmd="italic" title="@lang('admin.Italic')"><i class="bi bi-type-italic"></i></button>
        <button type="button" class="re-btn" data-cmd="underline" title="@lang('admin.Underline')"><i class="bi bi-type-underline"></i></button>
        <button type="button" class="re-btn" data-cmd="strikeThrough" title="@lang('admin.Strikethrough')"><i class="bi bi-type-strikethrough"></i></button>

        <span class="vr mx-1"></span>

        <button type="button" class="re-btn" data-block="h2" title="@lang('admin.Heading')">H2</button>
        <button type="button" class="re-btn" data-block="h3" title="@lang('admin.Subheading')">H3</button>
        <button type="button" class="re-btn" data-block="p" title="@lang('admin.Paragraph')"><i class="bi bi-paragraph"></i></button>

        <span class="vr mx-1"></span>

        <button type="button" class="re-btn" data-cmd="insertUnorderedList" title="@lang('admin.Bulleted list')"><i class="bi bi-list-ul"></i></button>
        <button type="button" class="re-btn" data-cmd="insertOrderedList" title="@lang('admin.Numbered list')"><i class="bi bi-list-ol"></i></button>
        <button type="button" class="re-btn" data-block="blockquote" title="@lang('admin.Quote')"><i class="bi bi-quote"></i></button>

        <span class="vr mx-1"></span>

        <button type="button" class="re-btn" data-action="link" title="@lang('admin.Insert link')"><i class="bi bi-link-45deg"></i></button>
        <button type="button" class="re-btn" data-cmd="unlink" title="@lang('admin.Remove link')"><i class="bi bi-link-45deg" style="text-decoration:line-through;"></i></button>
        <button type="button" class="re-btn" data-cmd="removeFormat" title="@lang('admin.Clear formatting')"><i class="bi bi-eraser"></i></button>

        <span class="ms-auto"></span>

        <button type="button" class="re-btn re-btn--toggle" data-action="source" title="@lang('admin.HTML source')">
            <i class="bi bi-code-slash"></i>
        </button>
    </div>

    <div id="{{ $editorId }}" class="rich-editor__area form-control" contenteditable="true" dir="{{ $dir }}"
        style="min-height:260px;border-top-left-radius:0;border-top-right-radius:0;overflow-y:auto;">{!! $value !!}</div>

    <textarea class="rich-editor__source form-control d-none" dir="ltr" rows="14"
        style="border-top-left-radius:0;border-top-right-radius:0;font-family:monospace;font-size:13px;"></textarea>

    <textarea name="{{ $name }}" class="rich-editor__input d-none">{{ $value }}</textarea>
</div>

@once
    @push('styles')
        <style>
            .rich-editor__toolbar {
                border-bottom: 0 !important;
            }

            .re-btn {
                border: 0;
                background: transparent;
                border-radius: 6px;
                padding: 4px 9px;
                font-size: 14px;
                font-weight: 600;
                color: #475569;
                line-height: 1.4;
            }

            .re-btn:hover {
                background: #e2e8f0;
                color: #2E3192;
            }

            .re-btn.is-active,
            .re-btn--toggle.is-active {
                background: #2E3192;
                color: #fff;
            }

            .rich-editor__area:focus {
                outline: 0;
                box-shadow: none;
                border-color: #2E3192;
            }

            .rich-editor__area h2 {
                font-size: 1.2rem;
                font-weight: 700;
                color: #2E3192;
                margin: 1.2rem 0 .5rem;
            }

            .rich-editor__area h3 {
                font-size: 1.05rem;
                font-weight: 700;
                margin: 1rem 0 .4rem;
            }

            .rich-editor__area p {
                margin-bottom: .75rem;
            }

            .rich-editor__area ul {
                list-style: disc;
                padding-inline-start: 1.5rem;
                margin-bottom: .75rem;
            }

            .rich-editor__area ol {
                list-style: decimal;
                padding-inline-start: 1.5rem;
                margin-bottom: .75rem;
            }

            .rich-editor__area blockquote {
                border-inline-start: 4px solid #9E0168;
                background: #f8f8fb;
                padding: .5rem .75rem;
                margin: .75rem 0;
            }

            .rich-editor__area table {
                width: 100%;
                border-collapse: collapse;
            }

            .rich-editor__area th,
            .rich-editor__area td {
                border: 1px solid #e5e7eb;
                padding: .4rem .6rem;
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            document.querySelectorAll('[data-rich-editor]').forEach(function (wrapper) {
                const area = wrapper.querySelector('.rich-editor__area');
                const source = wrapper.querySelector('.rich-editor__source');
                const input = wrapper.querySelector('.rich-editor__input');
                const toolbar = wrapper.querySelector('.rich-editor__toolbar');
                let sourceMode = false;

                // execCommand est déprécié mais reste le seul moyen sans dépendance externe.
                const exec = (cmd, value = null) => {
                    area.focus();
                    document.execCommand(cmd, false, value);
                    sync();
                };

                const sync = () => {
                    input.value = sourceMode ? source.value : area.innerHTML;
                };

                toolbar.addEventListener('click', function (e) {
                    const btn = e.target.closest('button');
                    if (!btn) return;

                    // Bascule code source <-> WYSIWYG
                    if (btn.dataset.action === 'source') {
                        sourceMode = !sourceMode;
                        btn.classList.toggle('is-active', sourceMode);
                        if (sourceMode) {
                            source.value = area.innerHTML;
                            area.classList.add('d-none');
                            source.classList.remove('d-none');
                        } else {
                            area.innerHTML = source.value;
                            source.classList.add('d-none');
                            area.classList.remove('d-none');
                        }
                        sync();
                        return;
                    }

                    if (sourceMode) return; // barre d'outils inactive en mode code

                    if (btn.dataset.action === 'link') {
                        const url = window.prompt(@json(__('admin.Enter the URL')), 'https://');
                        if (url) exec('createLink', url);
                        return;
                    }

                    if (btn.dataset.block) {
                        exec('formatBlock', '<' + btn.dataset.block + '>');
                        return;
                    }

                    if (btn.dataset.cmd) exec(btn.dataset.cmd);
                });

                // Reflète l'état des boutons selon la sélection
                const refreshState = () => {
                    if (sourceMode) return;
                    toolbar.querySelectorAll('[data-cmd]').forEach(function (btn) {
                        try {
                            btn.classList.toggle('is-active', document.queryCommandState(btn.dataset.cmd));
                        } catch (e) { /* commande non supportée */ }
                    });
                };

                area.addEventListener('input', sync);
                area.addEventListener('blur', sync);
                area.addEventListener('keyup', refreshState);
                area.addEventListener('mouseup', refreshState);
                source.addEventListener('input', sync);

                // Colle en texte brut pour éviter d'importer du HTML de Word
                area.addEventListener('paste', function (e) {
                    e.preventDefault();
                    const text = (e.clipboardData || window.clipboardData).getData('text/plain');
                    document.execCommand('insertText', false, text);
                    sync();
                });

                // Sécurité : resynchronise juste avant l'envoi du formulaire
                wrapper.closest('form')?.addEventListener('submit', sync);

                sync();
            });
        </script>
    @endpush
@endonce
