export default (e) => {
    const input = document.getElementById('profile_image');
    const label = document.querySelector('label[for="profile_image"]');


    const file = e.target.files[0];

    if (file) {
        const reader = new FileReader();

        reader.onload = (event) => {
            const dataUrl = event.target.result;
            label.innerHTML = `<img class='w-10 object-contain h-10 rounded-md overflow-hidden' src='${dataUrl}' alt='image' />`
        };

        reader.readAsDataURL(file);
    }
}
