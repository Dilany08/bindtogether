let profileDropdownList = document.querySelector(".profile-dropdown-list");
let btn = document.querySelector(".profile-dropdown-btn");

const toggle = () => profileDropdownList.classList.toggle("active");

window.addEventListener('click', function (e) {
    if(!btn.contains(e.target)) profileDropdownList.classList.remove("active");
});

let menu = document.querySelector('#menu-icon');
let navlist = document.querySelector('.navlist');

menu.onclick = () => {
    menu.classList.toggle('bx-x');
    navlist.classList.toggle('open');
};

const sr = ScrollReveal ({
    distance: '65px',
    duration: 2600,
    delay: 450,
    reset: true
})

sr.reveal('.content', {delay:200, origin:'top'});
sr.reveal('.bpsu-img', {delay:300, origin:'top'});
sr.reveal('.scroll-down', {delay:400, origin:'right'});
sr.reveal('.wrapper', {delay:400, origin:'top'});


$(document).ready(function() {
    $('body').on('click', '.like-btn', function() {
        var PostID = $(this).data('post-id');
        var is_liked = $(this).data('liked');
        var likeBtn = $(this);
        $.ajax({
            url: 'handle_like.php',
            type: 'POST',
            data: {
                PostID: PostID,
                is_liked: is_liked
            },
            success: function(response) {
                var data = JSON.parse(response);
                likeBtn.find('.like-count').text('(' + data.total_likes + ')');
                likeBtn.find('i').css('color', data.is_liked ? 'red' : '');
                likeBtn.data('liked', data.is_liked ? '1' : '0');
            }
        });
    });
});



