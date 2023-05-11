document.addEventListener("DOMContentLoaded",() => {
    
    /** Infinite fade effect */
    let containers_inf = document.querySelectorAll('.columns-fade-infinite');

    const observer = new IntersectionObserver(entries => {
    entries.forEach(entry => {
        console.log(entry);
        const intersecting = entry.isIntersecting;
        if(intersecting && entry) { 
            entry.target.classList.add('trigger');
        } else {
            entry.target.classList.remove('trigger');
        }
    }) }, {threshold: 0.75})

    if(containers_inf) {
        containers_inf.forEach(item => {
            observer.observe(item);
        })
    }

    /** Fade occurs only once */
    let containers_single = document.querySelectorAll('.columns-fade-single');

    const observer2 = new IntersectionObserver(entries => {
    entries.forEach(entry => {
        const intersecting = entry.isIntersecting;
        if(intersecting && entry) { 
            entry.target.classList.add('trigger');
        } 
    }) }, {threshold: 0.75})

    if(containers_single) {
        containers_single.forEach(item => {
            observer2.observe(item);
        })
    }

    /** Slide right occurs only once */
    let containers_right = document.querySelectorAll('.columns-slide-right');

    const observer3 = new IntersectionObserver(entries => {
    entries.forEach(entry => {
        const intersecting = entry.isIntersecting;
        if(intersecting && entry) { 
            entry.target.classList.add('trigger-slide');
        } 
    }) }, {threshold: 0.75})

    if(containers_right) {
        containers_right.forEach(item => {
            observer3.observe(item);
        })
    }

    /** Slide left occurs only once */
    let containers_left = document.querySelectorAll('.columns-slide-left');

    const observer4 = new IntersectionObserver(entries => {
    entries.forEach(entry => {
        const intersecting = entry.isIntersecting;
        if(intersecting && entry) { 
            entry.target.classList.add('trigger-slide');
        } 
    }) }, {threshold: 0.75})

    if(containers_left) {
        containers_left.forEach(item => {
            observer4.observe(item);
        })
    }

    /** Slide title in banner */
    let banner = document.querySelectorAll('.braftonium-banner.title-slide-left');

    const bannerObserver = new IntersectionObserver(entries => {
    entries.forEach(entry => {
        const intersecting = entry.isIntersecting;
        if(intersecting && entry) { 
            entry.target.classList.add('trigger-slide');
        } 
    }) }, {threshold: 0.75})

    if(banner) {
        banner.forEach(item => {
            bannerObserver.observe(item);
        })
    }
});