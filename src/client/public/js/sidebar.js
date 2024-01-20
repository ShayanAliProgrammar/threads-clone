export default () => {
    function sidebarCloseEffect(){
        const sidebar = document.getElementById('sidebar');
        const mainTag = document.querySelector('main');


        const handleScroll = ()=>{
            if (window.innerWidth >= 540) {
                sidebar.classList.remove('-translate-x-full');
                sidebar.classList.add('translate-x-0');
                sidebar.classList.remove('!h-0');
                sidebar.classList.remove('[visibility:hidden;]');
                sidebar.classList.remove('!w-0');
                mainTag.classList.remove('w-screen');
                setTimeout(() => {
                    sidebar.classList.remove('hidden');
                }, 200);
                return;
            };
            if (document.querySelector('main>section').scrollTop >= 1){
                sidebar.classList.add('-translate-x-full');
                sidebar.classList.remove('translate-x-0');
                sidebar.classList.add('!h-0');
                sidebar.classList.add('[visibility:hidden;]');
                sidebar.classList.add('!w-0');
                mainTag.classList.add('w-screen');
                setTimeout(() => {
                    sidebar.classList.add('hidden');
                }, 200);
            } else {
                sidebar.classList.remove('-translate-x-full');
                sidebar.classList.add('translate-x-0');
                sidebar.classList.remove('!h-0');
                sidebar.classList.remove('[visibility:hidden;]');
                sidebar.classList.remove('!w-0');
                mainTag.classList.remove('w-screen');
                setTimeout(() => {
                    sidebar.classList.remove('hidden');
                }, 200);
            }
        }

        if (document.querySelector('main>section').scrollTop >= 1) {
            handleScroll();
        }


        document.querySelector('main>section').addEventListener('scroll', handleScroll);
    }

    sidebarCloseEffect();

    swup.hooks.on('content:replace', sidebarCloseEffect);
}