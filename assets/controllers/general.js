"use strict"
const likeBtn = document.querySelector('.heart-icon');
const numberOfLikesElement = document.querySelector('.number-of-likes');
const buttonSubmit = document.getElementById("like");

let numberOfLikes = Number.parseInt(numberOfLikesElement.textContent, 10);
let isLiked = false;

// Functions

const likeClick = () => {
    if (!isLiked) {
        likeBtn.classList.add('isLiked');
        numberOfLikes++;
        numberOfLikesElement.textContent = numberOfLikes;
        isLiked = !isLiked;
        buttonSubmit.submit();
    } else {
        likeBtn.classList.remove('isLiked');
        numberOfLikes--;
        numberOfLikesElement.textContent = numberOfLikes;
        isLiked = !isLiked;
    }
};

// Event Listeners

likeBtn.addEventListener('click', likeClick);



