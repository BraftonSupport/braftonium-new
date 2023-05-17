document.addEventListener("DOMContentLoaded",() => {
    
    /** Infinite fade effect */
    let containers_inf = document.querySelectorAll('.columns-fade-infinite');

    const infiniteFadeObserver = new IntersectionObserver(entries => {
    entries.forEach(entry => {
        const intersecting = entry.isIntersecting;
        if(intersecting && entry) { 
            entry.target.classList.add('trigger');
        } else {
            entry.target.classList.remove('trigger');
        }
    }) }, {threshold: 0.75})

    if(containers_inf) {
        containers_inf.forEach(item => {
            infiniteFadeObserver.observe(item);
        })
    }

    /** Fade occurs only once */
    let containers_single = document.querySelectorAll('.columns-fade-single');

    const singleFadeObserver = new IntersectionObserver(entries => {
    entries.forEach(entry => {
        const intersecting = entry.isIntersecting;
        if(intersecting && entry) { 
            entry.target.classList.add('trigger');
        } 
    }) }, {threshold: 0.75})

    if(containers_single) {
        containers_single.forEach(item => {
            singleFadeObserver.observe(item);
        })
    }

    /** Slide right occurs only once */
    let containers_right = document.querySelectorAll('.columns-slide-right');

    const rightSlideObserver = new IntersectionObserver(entries => {
    entries.forEach(entry => {
        const intersecting = entry.isIntersecting;
        if(intersecting && entry) { 
            entry.target.classList.add('trigger-slide');
        } 
    }) }, {threshold: 0.75})

    if(containers_right) {
        containers_right.forEach(item => {
            rightSlideObserver.observe(item);
        })
    }

    /** Slide left occurs only once */
    let containers_left = document.querySelectorAll('.columns-slide-left');

    const leftSlideObserver = new IntersectionObserver(entries => {
    entries.forEach(entry => {
        const intersecting = entry.isIntersecting;
        if(intersecting && entry) { 
            entry.target.classList.add('trigger-slide');
        } 
    }) }, {threshold: 0.75})

    if(containers_left) {
        containers_left.forEach(item => {
            leftSlideObserver.observe(item);
        })
    }

    /** Banner Title Animations */
    let bannerSlide = document.querySelectorAll('.braftonium-banner.title-slide-right');
    let bannerFade = document.querySelectorAll('.braftonium-banner.title-fade-in');

    const bannerObserver = new IntersectionObserver(entries => {
    entries.forEach(entry => {
        const intersecting = entry.isIntersecting;
        if(intersecting && entry) { 
            entry.target.classList.add('trigger-slide');
        } 
    }) }, {threshold: 0.75})
    
    if(bannerSlide) {
        bannerSlide.forEach(item => {
            bannerObserver.observe(item);
        })
    }
    if(bannerFade) {
        bannerFade.forEach(item => {
            bannerObserver.observe(item);
        })
    }
});