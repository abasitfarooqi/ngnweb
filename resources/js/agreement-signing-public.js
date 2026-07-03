import jquery from 'jquery';
import 'bootstrap/dist/js/bootstrap.bundle.min.js';

window.$ = window.jQuery = jquery;

function collectSigningForms() {
    const forms = new Set();

    document.querySelectorAll('.agreement-signature-modal-root form').forEach(function (form) {
        forms.add(form);
    });

    document.querySelectorAll('.agreement-signing-page form').forEach(function (form) {
        if (form.querySelector('#sigpad, .agreement-signature-modal-root, .kbw-signature, .e-signpad')) {
            forms.add(form);
        }
    });

    return forms;
}

function bindSigningForm(form) {
    if (form.dataset.signingAjaxBound === '1') {
        return;
    }

    form.dataset.signingAjaxBound = '1';

    form.addEventListener('submit', function (event) {
        event.preventDefault();

        const signInput = form.querySelector('input.sign, input[name="sign"]');
        if (!signInput || !signInput.value) {
            window.alert('Please provide a signature.');
            return;
        }

        const submitBtn = form.querySelector('.sign-pad-button-submit');
        const successEl = form.querySelector('#success-message')
            || form.closest('.agreement-signature-modal-root')?.querySelector('#success-message');
        const originalSubmitLabel = submitBtn ? submitBtn.textContent : '';
        const host = form.closest('.modal-content') || form;

        if (host && getComputedStyle(host).position === 'static') {
            host.style.position = 'relative';
        }

        let overlay = host.querySelector('.agreement-signing-loading');
        if (!overlay) {
            overlay = document.createElement('div');
            overlay.className = 'agreement-signing-loading';
            overlay.innerHTML = ''
                + '<div class="agreement-signing-spinner" role="status" aria-label="Loading"></div>'
                + '<p class="agreement-signing-loading-title">Generating your signed document…</p>'
                + '<p class="agreement-signing-loading-hint">Please keep this page open — this usually takes 10–30 seconds.</p>'
                + '<p class="agreement-signing-loading-elapsed"></p>';
            host.appendChild(overlay);
        }

        const pdfCount = form.dataset.signingPdfCount;
        if (pdfCount && overlay.querySelector('.agreement-signing-loading-hint')) {
            overlay.querySelector('.agreement-signing-loading-hint').textContent =
                'Please keep this page open — generating ' + pdfCount + ' contract PDFs usually takes 10–30 seconds.';
        }

        overlay.hidden = false;
        const elapsedEl = overlay.querySelector('.agreement-signing-loading-elapsed');
        const startedAt = Date.now();

        const elapsedTimer = window.setInterval(function () {
            const seconds = Math.floor((Date.now() - startedAt) / 1000);
            if (elapsedEl) {
                elapsedEl.textContent = seconds > 0 ? 'Working… ' + seconds + 's' : '';
            }
        }, 1000);

        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.textContent = 'Submitting…';
        }

        if (successEl) {
            successEl.textContent = '';
        }

        const controller = new AbortController();
        const timeoutId = window.setTimeout(function () {
            controller.abort();
        }, 180000);

        function resetSubmitUi() {
            window.clearInterval(elapsedTimer);
            window.clearTimeout(timeoutId);
            overlay.hidden = true;

            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.textContent = originalSubmitLabel || 'Submit';
            }
        }

        fetch(form.action, {
            method: 'POST',
            body: new FormData(form),
            signal: controller.signal,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                Accept: 'application/json',
            },
        })
            .then(function (response) {
                if (!response.ok) {
                    return response.json().then(function (body) {
                        throw new Error(body.message || 'Signing failed (' + response.status + ').');
                    }).catch(function (parseError) {
                        if (parseError instanceof Error && parseError.message.indexOf('Signing failed') === 0) {
                            throw parseError;
                        }
                        throw new Error('Signing failed (' + response.status + ').');
                    });
                }

                return response.json();
            })
            .then(function (data) {
                window.clearInterval(elapsedTimer);
                window.clearTimeout(timeoutId);

                if (successEl) {
                    successEl.textContent = data.message || 'Contract signed successfully.';
                }

                if (overlay.querySelector('.agreement-signing-loading-title')) {
                    overlay.querySelector('.agreement-signing-loading-title').textContent = 'Signed successfully';
                }
                if (overlay.querySelector('.agreement-signing-loading-hint')) {
                    overlay.querySelector('.agreement-signing-loading-hint').textContent =
                        'You can close this window. Copies will be emailed to you.';
                }
                if (elapsedEl) {
                    elapsedEl.textContent = '';
                }
                overlay.classList.add('agreement-signing-loading--done');

                if (submitBtn) {
                    submitBtn.textContent = 'Signed';
                }
            })
            .catch(function (error) {
                resetSubmitUi();

                if (error.name === 'AbortError') {
                    window.alert('Signing is taking too long. Please refresh the page and try again, or contact us if this continues.');
                    return;
                }

                window.alert(error.message || 'Could not submit signature. Please try again.');
            });
    });
}

document.addEventListener('DOMContentLoaded', function () {
    collectSigningForms().forEach(bindSigningForm);
});
