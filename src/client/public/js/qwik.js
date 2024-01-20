const swup = new Swup({
	native: true,
	cache: false,
	plugins: [
		new SwupHeadPlugin(),
		new SwupFormsPlugin()
	],
});

const qwikLikeApp = async () => {
	// Load and run scripts dynamically
	async function loadAndRunScript(scriptFileName, scriptType, e = null) {
		try {
			const scriptResponse = await import(`${scriptFileName}.${scriptType}`);
			scriptResponse.default(e??undefined);
			return;
		} catch (error) {
			console.error('Error loading script:', error);
		}
	}

	// Handle intersection observer events
	function handleIntersection(entries, observer, element = null) {
		entries.forEach((entry) => {
			if (entry.isIntersecting) {
				document.querySelectorAll('*').forEach((element) => {
					const attributes = element.attributes;
					for (let i = 0; i < attributes.length; i++) {
						const attributeName = attributes[i].name;
						const attributeValue = attributes[i].value;
						const eventType = attributeName.substring(3);

						if (attributeName.startsWith('on:') && eventType==='visible') {
							const [scriptFileName, scriptType] = attributeValue.split('.');

							// Load and run the script dynamically
							loadAndRunScript(scriptFileName, scriptType, element);

							window.addEventListener('popstate', () => {
								handleIntersection(entries, observer, element);
							});

							observer.unobserve(entry.target);
						}
					}
				});
			}
		});
	}

	// Create intersection observer
	const intersectionObserver = new IntersectionObserver(handleIntersection, {
		threshold: 0.5, // Adjust the threshold as needed
		root: document.querySelector('main>section'),
		rootMargin: "0px"
	});

	// Add event listeners to elements based on their attributes
	document.querySelectorAll('*').forEach((element) => {
		const attributes = element.attributes;
		for (let i = 0; i < attributes.length; i++) {
			const attributeName = attributes[i].name;
			const attributeValue = attributes[i].value;

			if (attributeName.startsWith('on:')) {
				const eventType = attributeName.substring(3);
				const [scriptFileName, scriptType] = attributeValue.split('.');

				if (eventType !== 'visible') {
					element.addEventListener(eventType, (e) => {
						// Load and run the script dynamically
						loadAndRunScript(scriptFileName, scriptType, e);
					});
				}
				if (eventType === 'visible') {
					// Observe the element for intersection
					intersectionObserver.observe(element);
				}
			}
		}
	});
};

document.addEventListener('DOMContentLoaded', ()=>{
	qwikLikeApp();
});

swup.hooks.on('page:view', qwikLikeApp);