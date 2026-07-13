(function () {
    'use strict';

    class NWPCChatbot {
        constructor(config) {
            this.lang = config.lang || 'si';
            this.knowledgeBase = config.knowledgeBase || [];
            this.departments = config.departments || [];
            this.i18n = config.i18n || {};
            this.llmEnabled = Boolean(config.llmEnabled);
            this.isOpen = false;
            this.isBusy = false;
            this.welcomeRendered = false;
            this.selectedDepartment = null;
            this.usedSuggestionIds = new Set();

            this.els = {
                backdrop: document.getElementById('nwpcChatBackdrop'),
                launcher: document.getElementById('nwpcChatLauncher'),
                panel: document.getElementById('nwpcChatPanel'),
                messages: document.getElementById('nwpcChatMessages'),
                input: document.getElementById('nwpcChatInput'),
                send: document.getElementById('nwpcChatSend'),
                close: document.getElementById('nwpcChatClose'),
                reset: document.getElementById('nwpcChatReset'),
                whatsapp: document.getElementById('nwpcWhatsappFab'),
            };
        }

        init() {
            if (!this.els.panel || !this.els.messages) return;

            this.els.launcher?.addEventListener('click', () => this.open());
            this.els.close?.addEventListener('click', () => this.close());
            this.els.reset?.addEventListener('click', () => this.resetConversation());
            this.els.send?.addEventListener('click', () => this.sendMessage());
            this.els.backdrop?.addEventListener('click', () => this.close());

            this.els.input?.addEventListener('input', () => this.onInputChange());
            this.els.input?.addEventListener('keydown', (e) => {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    this.sendMessage();
                }
            });

            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && this.isOpen) this.close();
            });

            // Delegate browse UI clicks inside messages
            this.els.messages.addEventListener('click', (e) => this.onBrowseClick(e));
        }

        onInputChange() {
            const value = this.els.input.value.trim();
            this.els.send.disabled = !value || this.isBusy;
            this.autoResizeInput();
        }

        autoResizeInput() {
            const input = this.els.input;
            input.style.height = 'auto';
            input.style.height = Math.min(input.scrollHeight, 96) + 'px';
        }

        open() {
            this.isOpen = true;
            this.els.panel.classList.add('is-open');
            this.els.backdrop?.classList.add('is-visible');
            this.els.launcher?.classList.add('is-hidden');
            this.els.launcher?.setAttribute('aria-expanded', 'true');
            this.els.panel.setAttribute('aria-hidden', 'false');
            this.els.backdrop?.setAttribute('aria-hidden', 'false');
            document.body.style.overflow = 'hidden';
            if (this.els.whatsapp) this.els.whatsapp.style.display = 'none';

            if (!this.welcomeRendered) {
                this.renderWelcome();
                this.welcomeRendered = true;
            }

            setTimeout(() => this.els.input?.focus(), 280);
        }

        close() {
            this.isOpen = false;
            this.els.panel.classList.remove('is-open');
            this.els.backdrop?.classList.remove('is-visible');
            this.els.launcher?.classList.remove('is-hidden');
            this.els.launcher?.setAttribute('aria-expanded', 'false');
            this.els.panel.setAttribute('aria-hidden', 'true');
            this.els.backdrop?.setAttribute('aria-hidden', 'true');
            document.body.style.overflow = '';
            if (this.els.whatsapp) this.els.whatsapp.style.display = '';
            this.els.launcher?.focus();
        }

        resetConversation() {
            this.selectedDepartment = null;
            this.usedSuggestionIds = new Set();
            this.els.messages.innerHTML = '';
            this.welcomeRendered = false;
            this.renderWelcome();
            this.welcomeRendered = true;
            this.els.input.value = '';
            this.onInputChange();
            this.els.input?.focus();
        }

        getDepartmentById(id) {
            return this.departments.find((d) => d.id === id) || null;
        }

        getDepartmentLabel(dept) {
            if (!dept) return '';
            return dept.labels?.[this.lang] || dept.labels?.en || dept.id;
        }

        getDepartmentQuestions(deptId, options = {}) {
            const dept = this.getDepartmentById(deptId);
            if (!dept) return [];

            const prefix = dept.prefix;
            const excludeUsed = options.excludeUsed !== false;
            const limit = options.limit || 6;

            const priority = (id) => {
                if (id.endsWith('_about')) return 0;
                if (id.includes('complaint') || id.includes('contact') || id.includes('officers')) return 2;
                if (id.includes('service_times') || id.includes('service_standards')) return 3;
                if (id.includes('citizen_charter')) return 4;
                return 1;
            };

            const items = this.knowledgeBase
                .filter((item) => item.id?.startsWith(prefix) && item.suggestions?.[this.lang])
                .filter((item) => !(excludeUsed && this.usedSuggestionIds.has(item.id)))
                .sort((a, b) => {
                    const pa = priority(a.id);
                    const pb = priority(b.id);
                    if (pa !== pb) return pa - pb;
                    return a.id.localeCompare(b.id);
                });

            return items.slice(0, limit).map((item) => ({
                id: item.id,
                text: item.suggestions[this.lang],
            }));
        }

        renderWelcome() {
            const browseHtml = `
                <div class="nwpc-chat-browse" id="nwpcChatBrowse" data-view="departments">
                    ${this.buildDepartmentPickerHtml()}
                </div>`;

            this.appendBotMessage(
                `${escapeHtml(this.i18n.welcome || '')}${browseHtml}`,
                { raw: true, showTime: false }
            );
        }

        buildDepartmentPickerHtml() {
            const label = escapeHtml(this.i18n.chooseDepartment || 'Choose a department');
            const tiles = this.departments.map((dept) => {
                const name = escapeHtml(this.getDepartmentLabel(dept));
                const icon = escapeHtml(dept.icon || 'fa-building');
                return `
                    <button type="button" class="nwpc-chat-dept" data-dept-id="${escapeHtml(dept.id)}">
                        <span class="nwpc-chat-dept__icon" aria-hidden="true"><i class="fas ${icon}"></i></span>
                        <span class="nwpc-chat-dept__name">${name}</span>
                    </button>`;
            }).join('');

            return `
                <div class="nwpc-chat-browse__panel nwpc-chat-browse__panel--depts">
                    <p class="nwpc-chat-browse__label">${label}</p>
                    <div class="nwpc-chat-dept-grid">${tiles}</div>
                </div>`;
        }

        buildDepartmentQuestionsHtml(deptId) {
            const dept = this.getDepartmentById(deptId);
            if (!dept) return this.buildDepartmentPickerHtml();

            const questions = this.getDepartmentQuestions(deptId, { excludeUsed: false, limit: 6 });
            const backLabel = escapeHtml(this.i18n.back || 'Back');
            const changeLabel = escapeHtml(this.i18n.changeDepartment || 'Change department');
            const qLabel = escapeHtml(this.i18n.suggestedQuestions || 'Suggested questions');
            const deptName = escapeHtml(this.getDepartmentLabel(dept));

            const chips = questions.map((q) => `
                <button type="button" class="nwpc-chat-chip" data-suggestion-id="${escapeHtml(q.id)}" data-suggestion-text="${escapeHtml(q.text)}">
                    ${escapeHtml(q.text)}
                </button>`).join('');

            return `
                <div class="nwpc-chat-browse__panel nwpc-chat-browse__panel--questions">
                    <div class="nwpc-chat-browse__header">
                        <button type="button" class="nwpc-chat-browse__back" data-action="back-depts" aria-label="${backLabel}">
                            <i class="fas fa-arrow-left" aria-hidden="true"></i>
                            <span>${backLabel}</span>
                        </button>
                        <p class="nwpc-chat-browse__dept-title">${deptName}</p>
                    </div>
                    <p class="nwpc-chat-browse__label">${qLabel}</p>
                    <div class="nwpc-chat-topics__list">${chips || `<p class="nwpc-chat-browse__empty">${escapeHtml(this.i18n.fallback || '')}</p>`}</div>
                    <button type="button" class="nwpc-chat-browse__change" data-action="back-depts">${changeLabel}</button>
                </div>`;
        }

        showDepartmentPicker() {
            this.selectedDepartment = null;
            const browse = document.getElementById('nwpcChatBrowse');
            if (browse && !browse.classList.contains('is-collapsed')) {
                browse.dataset.view = 'departments';
                browse.innerHTML = this.buildDepartmentPickerHtml();
                this.scrollToBottom();
                return;
            }
            this.appendBotMessage(
                `<div class="nwpc-chat-browse" id="nwpcChatBrowse" data-view="departments">${this.buildDepartmentPickerHtml()}</div>`,
                { raw: true, showTime: false }
            );
        }

        showDepartmentQuestions(deptId) {
            this.selectedDepartment = deptId;
            const html = this.buildDepartmentQuestionsHtml(deptId);
            let browse = document.getElementById('nwpcChatBrowse');
            if (browse?.classList.contains('is-collapsed')) {
                browse.removeAttribute('id');
                browse = null;
            }
            if (!browse) {
                this.appendBotMessage(
                    `<div class="nwpc-chat-browse" id="nwpcChatBrowse" data-view="questions">${html}</div>`,
                    { raw: true, showTime: false }
                );
                return;
            }
            browse.dataset.view = 'questions';
            browse.classList.remove('is-collapsed');
            browse.innerHTML = html;
            this.scrollToBottom();
        }

        onBrowseClick(e) {
            const deptBtn = e.target.closest('[data-dept-id]');
            if (deptBtn) {
                e.preventDefault();
                this.showDepartmentQuestions(deptBtn.getAttribute('data-dept-id'));
                return;
            }

            const backBtn = e.target.closest('[data-action="back-depts"]');
            if (backBtn) {
                e.preventDefault();
                this.showDepartmentPicker();
                return;
            }

            const chip = e.target.closest('[data-suggestion-text]');
            if (chip && !this.isBusy) {
                e.preventDefault();
                const text = chip.getAttribute('data-suggestion-text');
                const id = chip.getAttribute('data-suggestion-id');
                if (id) this.usedSuggestionIds.add(id);
                this.sendMessage(text);
            }
        }

        renderFollowUps(intent) {
            let deptId = this.selectedDepartment;
            if (!deptId && intent?.id) {
                const match = this.departments.find((d) => intent.id.startsWith(d.prefix));
                if (match) {
                    deptId = match.id;
                    this.selectedDepartment = deptId;
                }
            }
            if (!deptId) return;

            const questions = this.getDepartmentQuestions(deptId, { excludeUsed: true, limit: 4 });
            if (!questions.length) return;

            const dept = this.getDepartmentById(deptId);
            const label = escapeHtml(this.i18n.moreQuestions || 'More questions');
            const changeLabel = escapeHtml(this.i18n.changeDepartment || 'Change department');
            const deptName = escapeHtml(this.getDepartmentLabel(dept));

            const chips = questions.map((q) => `
                <button type="button" class="nwpc-chat-chip" data-suggestion-id="${escapeHtml(q.id)}" data-suggestion-text="${escapeHtml(q.text)}">
                    ${escapeHtml(q.text)}
                </button>`).join('');

            const html = `
                <div class="nwpc-chat-followups">
                    <p class="nwpc-chat-browse__label">${label} · ${deptName}</p>
                    <div class="nwpc-chat-topics__list">${chips}</div>
                    <button type="button" class="nwpc-chat-browse__change" data-action="back-depts">${changeLabel}</button>
                </div>`;

            this.appendBotMessage(html, { raw: true, showTime: false, scroll: false });
        }

        getTimeLabel() {
            return new Date().toLocaleTimeString(
                this.lang === 'en' ? 'en-LK' : this.lang === 'ta' ? 'ta-LK' : 'si-LK',
                { hour: '2-digit', minute: '2-digit' }
            );
        }

        appendUserMessage(text) {
            const html = `
                <div class="nwpc-chat-msg nwpc-chat-msg--user">
                    <div class="nwpc-chat-msg__avatar" aria-hidden="true"><i class="fas fa-user"></i></div>
                    <div class="nwpc-chat-msg__bubble">
                        ${escapeHtml(text)}
                        <span class="nwpc-chat-msg__time">${this.getTimeLabel()}</span>
                    </div>
                </div>`;
            return this.insertMessage(html);
        }

        appendBotMessage(content, options = {}) {
            const body = options.raw ? content : linkifyText(content);
            const time = options.showTime !== false
                ? `<span class="nwpc-chat-msg__time">${this.getTimeLabel()}</span>`
                : '';

            const html = `
                <div class="nwpc-chat-msg nwpc-chat-msg--bot">
                    <div class="nwpc-chat-msg__avatar" aria-hidden="true"><i class="fas fa-landmark"></i></div>
                    <div class="nwpc-chat-msg__bubble">
                        ${body}
                        ${time}
                    </div>
                </div>`;
            return this.insertMessage(html, { scroll: options.scroll !== false });
        }

        insertMessage(html, options = {}) {
            this.els.messages.insertAdjacentHTML('beforeend', html);
            const el = this.els.messages.lastElementChild;
            if (options.scroll !== false) {
                this.scrollToBottom();
            }
            return el;
        }

        scrollToBottom() {
            this.els.messages.scrollTop = this.els.messages.scrollHeight;
        }

        /** Keep the answer near the top of the viewport so follow-ups sit below without stealing focus. */
        scrollToAnswer(el) {
            if (!el || !this.els.messages) return;
            const container = this.els.messages;
            const cRect = container.getBoundingClientRect();
            const eRect = el.getBoundingClientRect();
            const top = eRect.top - cRect.top + container.scrollTop - 12;
            container.scrollTo({
                top: Math.max(0, top),
                behavior: 'smooth',
            });
        }

        showTyping() {
            const html = `
                <div id="nwpcChatTyping" class="nwpc-chat-msg nwpc-chat-msg--bot nwpc-chat-typing">
                    <div class="nwpc-chat-msg__avatar" aria-hidden="true"><i class="fas fa-landmark"></i></div>
                    <div class="nwpc-chat-msg__bubble" aria-label="${escapeHtml(this.i18n.typing || 'Typing')}">
                        <span></span><span></span><span></span>
                    </div>
                </div>`;
            this.insertMessage(html);
        }

        hideTyping() {
            document.getElementById('nwpcChatTyping')?.remove();
        }

        async resolveIntent(text) {
            const matching = window.NWPCMatching;
            if (!matching?.detectIntentDetailed) {
                const intent = matching?.detectIntent?.(text, this.knowledgeBase) || null;
                return {
                    intent,
                    diagnostics: {
                        score: intent?._matchScore ?? 0,
                        secondScore: 0,
                        confident: Boolean(intent),
                        softMatch: false,
                        candidates: intent ? [{ id: intent.id, score: intent._matchScore || 0 }] : [],
                        llmTried: false,
                        reason: intent ? 'matched' : 'no_match',
                    },
                };
            }

            const detailed = matching.detectIntentDetailed(text, this.knowledgeBase);
            const diagnostics = {
                score: detailed.score || 0,
                secondScore: detailed.secondScore || 0,
                confident: Boolean(detailed.confident),
                softMatch: Boolean(detailed.softMatch),
                candidates: detailed.candidates || [],
                llmTried: false,
                reason: 'no_match',
            };

            if (detailed.confident && detailed.intent) {
                diagnostics.reason = 'local_confident';
                return { intent: detailed.intent, diagnostics };
            }

            if (this.llmEnabled) {
                diagnostics.llmTried = true;
                try {
                    const res = await fetch('match_intent.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({
                            question: text,
                            lang: this.lang,
                            candidates: detailed.candidates || [],
                        }),
                    });
                    const data = await res.json();
                    if (data?.ok && data.intent_id) {
                        const found = this.knowledgeBase.find((e) => e.id === data.intent_id);
                        if (found) {
                            diagnostics.reason = 'llm';
                            return { intent: found, diagnostics };
                        }
                    }
                } catch (e) {
                    // Fall through to soft local match
                }
            }

            if (detailed.softMatch && detailed.intent) {
                diagnostics.reason = 'local_soft';
                return { intent: detailed.intent, diagnostics };
            }

            if (detailed.intent) {
                diagnostics.reason = 'below_threshold';
            }

            return { intent: detailed.intent || null, diagnostics };
        }

        async sendMessage(overrideText) {
            const text = (overrideText ?? this.els.input.value).trim();
            if (!text || this.isBusy) return;

            this.isBusy = true;
            this.els.send.disabled = true;
            this.els.input.value = '';
            this.autoResizeInput();

            // Hide active browse panel after a question is asked (follow-ups come later)
            const browse = document.getElementById('nwpcChatBrowse');
            if (browse) browse.classList.add('is-collapsed');

            this.appendUserMessage(text);
            this.showTyping();

            const { intent, diagnostics } = await this.resolveIntent(text);
            const delay = 400 + Math.random() * 300;

            setTimeout(async () => {
                this.hideTyping();
                const reply = intent
                    ? (intent.responses[this.lang] || intent.responses.en || intent.responses.si)
                    : (this.i18n.fallback || '');

                const answerEl = this.appendBotMessage(reply, { scroll: false });

                if (intent) {
                    if (intent.id) this.usedSuggestionIds.add(intent.id);
                    this.renderFollowUps(intent);
                } else {
                    const dept = this.getDepartmentById(this.selectedDepartment);
                    await logUnknownQuestion(text, this.lang, {
                        department: dept
                            ? {
                                id: dept.id,
                                name: this.getDepartmentLabel(dept),
                            }
                            : null,
                        diagnostics,
                    });
                    this.appendBotMessage(
                        `<button type="button" class="nwpc-chat-browse__change" data-action="back-depts">${escapeHtml(this.i18n.changeDepartment || 'Change department')}</button>`,
                        { raw: true, showTime: false, scroll: false }
                    );
                }

                // After follow-ups are added, pin viewport to the answer (not the bottom chips)
                requestAnimationFrame(() => this.scrollToAnswer(answerEl));

                this.isBusy = false;
                this.onInputChange();
                this.els.input?.focus({ preventScroll: true });
            }, delay);
        }
    }

    function escapeHtml(text) {
        return String(text)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }

    function linkifyText(text) {
        const escaped = escapeHtml(text);
        const withBreaks = escaped.replace(/\n/g, '<br>');
        return withBreaks.replace(
            /(https?:\/\/[^\s<]+)/g,
            '<a href="$1" target="_blank" rel="noopener noreferrer">$1</a>'
        );
    }

    async function logUnknownQuestion(question, lang, meta = {}) {
        if (question.length < 4) return;
        const diagnostics = meta.diagnostics || {};
        try {
            await fetch('log_unknown.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    question,
                    lang,
                    reason: diagnostics.reason || 'no_match',
                    department: meta.department || null,
                    score: diagnostics.score ?? null,
                    second_score: diagnostics.secondScore ?? null,
                    confident: diagnostics.confident ?? null,
                    soft_match: diagnostics.softMatch ?? null,
                    llm_tried: diagnostics.llmTried ?? null,
                    candidates: (diagnostics.candidates || []).slice(0, 5),
                    timestamp: new Date().toISOString(),
                }),
            });
        } catch (e) {
            // Non-blocking
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        const app = new NWPCChatbot(window.CHATBOT_CONFIG || {});
        app.init();
        window.nwpcChatbot = app;
    });
})();
