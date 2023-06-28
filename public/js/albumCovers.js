const covers = document.querySelectorAll('.album__cover--thumbnail');
covers.forEach(cover => {
    const coverSrc = cover.src;
    const pseudoElement = cover.parentNode.querySelector('.album__cover_inner');
    pseudoElement.dataset.coverUrl = coverSrc;
})