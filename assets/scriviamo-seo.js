const { registerPlugin } = wp.plugins;
const { PluginSidebar, PluginSidebarMoreMenuItem } = wp.editPost;
const { Button, PanelBody, Notice, Spinner } = wp.components; // Importa Spinner
const { createElement, useState, useEffect } = wp.element;
const { select, dispatch, subscribe } = wp.data;
const apiFetch = wp.apiFetch;

const ScriviamoSEOButton = () => {
    const [isNewPost, setIsNewPost] = useState(false);
    const [token, setToken] = useState(ScriviamoSeoButton.token || '');
    const [isLoading, setIsLoading] = useState(false); // Stato di caricamento

    useEffect(() => {
        const checkIfNewPost = () => {
            const isPostNew = select('core/editor').isEditedPostNew();
            setIsNewPost(isPostNew);
        };

        // Initial check
        checkIfNewPost();

        // Subscribe to changes
        const unsubscribe = subscribe(() => {
            checkIfNewPost();
        });

        return () => unsubscribe();
    }, []);

    const updateYoastSEO = async () => {
        setIsLoading(true); // Imposta lo stato di caricamento su true
        const postId = select('core/editor').getCurrentPostId();
        const postTitle = select('core/editor').getEditedPostAttribute('title');
        const postContent = select('core/editor').getEditedPostAttribute('content');

        try {
            // Chiamata API per generare il titolo SEO
            const scriviamoResponseTitle = await fetch('https://api.scriviamo.ai/api/generate/seo-title', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${token}`,
                },
                body: JSON.stringify({
                    inputText: postContent,
                }),
            });

            // Chiamata API per generare la descrizione SEO
            const scriviamoResponseDescription = await fetch('https://api.scriviamo.ai/api/generate/seo-description', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${token}`,
                },
                body: JSON.stringify({
                    inputText: postContent,
                }),
            });

            const scriviamoDataTitle = await scriviamoResponseTitle.json();
            const scriviamoDataDescription = await scriviamoResponseDescription.json();

            if(scriviamoDataTitle.message === "Piano non sufficente"){
                alert("Il tuo piano attuale non comprende questo servizio. Per utilizzare il pugin ScrivIAmo SEO devi sottoscrivere l'abbonamento Agency o Custom.");
            }
            if(scriviamoDataDescription.message === "Piano non sufficente"){
                alert("Il tuo piano attuale non comprende questo servizio. Per utilizzare il pugin ScrivIAmo SEO devi sottoscrivere l'abbonamento Agency o Custom.");
            }

            console.log("scriviamoDataTitle", scriviamoDataTitle);
            console.log("scriviamoDataDescription", scriviamoDataDescription);

            if (!scriviamoResponseTitle.ok || !scriviamoResponseDescription.ok) {
                throw new Error('Errore durante la generazione del seo title o description.');
            }

            const generatedTitle = scriviamoDataTitle.results;
            const generatedDescription = `${scriviamoDataDescription.results.substring(0, 155)}`;

            const response = await apiFetch({
                path: '/scriviamo-seo-button/v1/update-seo',
                method: 'POST',
                headers: {
                    'X-WP-Nonce': ScriviamoSeoButton.nonce,
                },
                data: {
                    post_id: postId,
                    title: generatedTitle,
                    description: generatedDescription,
                },
            });

            if (response === 'SEO fields updated') {
                // Update Yoast SEO fields in the DOM
                const titleElement = document.getElementById('yoast-google-preview-title-metabox');
                const metaTitle = document.getElementById('yoast_wpseo_title');

                if (titleElement) {
                    titleElement.innerText = generatedTitle;
                }
                if (metaTitle) {
                    metaTitle.value = generatedTitle;
                }

                const descElement = document.getElementById('yoast-google-preview-description-metabox');
                const metaDesc = document.getElementById('yoast_wpseo_metadesc');
                if (descElement) {
                    descElement.innerText = generatedDescription;
                }
                if (metaDesc) {
                    metaDesc.value = generatedDescription;
                }

                // Update the snippet preview SEO
                const snippetPreviewContainer = document.getElementById('yoast-snippet-preview-container');
                if (snippetPreviewContainer) {
                    // SEO Title
                    const titleElementPreview = snippetPreviewContainer.querySelector('div:nth-child(2) .sc-Dmqmp.sc-dxtBLK span');
                    if (titleElementPreview) {
                        titleElementPreview.innerText = generatedTitle;
                    }
                    // SEO Description
                    const descElementPreview = snippetPreviewContainer.querySelector('div:nth-child(3) .sc-bjCGfv.fxouZ span');
                    if (descElementPreview) {
                        descElementPreview.nextSibling.textContent = generatedDescription;
                    }
                }

                // trigger the save to make sure it persists immediately
                await dispatch('core/editor').savePost();

                console.log('Scriviamo Seo aggiornato con successo.');

                //reload page
                setTimeout(() =>{
                    location.reload();
                }, 1000);

            } else {
                alert("Errore 001: contatta l'assistenza da scriviamo.ai");
                console.error('Failed to update Yoast SEO fields: Unexpected response', response);
            }
        } catch (error) {
            alert("Errore 002: Accertati di aver inserito il campo token nelle impostazioni del plugin, recuperando il token dalla tua area utente di scriviamo.ai. Se il problema persiste contatta l'assistenza da scriviamo.ai");
            console.error('Failed to update Yoast SEO fields', error);
        } finally {
            setIsLoading(false); // Imposta lo stato di caricamento su false
        }
    };


    return createElement(
        wp.element.Fragment,
        null,
        createElement(
            PluginSidebarMoreMenuItem,
            {
                target: 'scriviamo-seo-sidebar',
            },
            'Scriviamo.ai SEO Generator'
        ),
        createElement(
            PluginSidebar,
            {
                name: 'scriviamo-seo-sidebar',
                title: 'Scriviamo.ai SEO Generator',
            },
            createElement(
                PanelBody,
                null,
                isNewPost
                    ? createElement(Notice, { status: 'info', isDismissible: false }, 'Salva prima il post per abilitare la generazione seo di Scriviamo.')
                    : createElement(Button, { isPrimary: true, onClick: updateYoastSEO, isBusy: isLoading }, isLoading ? createElement(Spinner, null) : 'Genera il seo con Scriviamo.ai')
            )
        )
    );
};

registerPlugin('scriviamo-seo-button', {
    render: ScriviamoSEOButton,
});
