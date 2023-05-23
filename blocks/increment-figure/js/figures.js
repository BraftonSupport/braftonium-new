document.addEventListener("DOMContentLoaded",() => {
    const sleep = (speed) => {
        return new Promise(resolve => setTimeout(resolve, speed))
    }
  
    let figures = document.querySelectorAll('.figure-increment span.figure-placeholder');
  
    function increment() {
        figures.forEach((item)=>{
        loop(item)
        });
    }
  
    async function loop(item) {
        let start = item.getAttribute('data-start');
        let end = parseInt(item.getAttribute('data-end'))+1;
        let speed = item.getAttribute('data-speed');
        for(let i=start; i < end; i++){
            await sleep(speed);
            console.log(i);
            item.textContent = i;
        }
    }

    const observer = new IntersectionObserver(entries => {
            entries.forEach(entry => {
            const intersecting = entry.isIntersecting;
            if(intersecting && entry.target.classList.contains('activated') == false) {
                entry.target.classList.add('activated');
                increment();
            }
        })
        },{threshold: 0.75}
    )

    if(figures) {
        figures.forEach(item => {
            observer.observe(item);
        })
    }
});