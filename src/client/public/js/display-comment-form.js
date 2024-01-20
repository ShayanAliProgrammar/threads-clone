export default () => {
    if (document.getElementById('comment-form').classList.contains('!hidden')){
        document.getElementById('comment-form').classList.remove('!hidden')
    } else {
        document.getElementById('comment-form').classList.add('!hidden')
    }
};