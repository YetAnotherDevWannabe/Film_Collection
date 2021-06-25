document.querySelector('#poster').addEventListener('click', function (){
    displayPoster();
})

function displayPoster()
{
    // Create the new overlayDiv
    let overlay = document.createElement('div');
    document.body.append(overlay);
    // Add the 'overlay' class to the new div
    overlay.classList.add('overlay');


    // // Create the new img
    let overlayImage = document.createElement('img');
    let poster = document.querySelector('#poster')
    let posterAttr = poster.getAttribute("src")
    overlayImage.src = posterAttr;
    console.log(overlayImage);
    // Insert newly created image to overlay
    overlay.append(overlayImage);


    function removeOverlay(){
        let divToRemove = document.querySelector('body>.overlay');
        divToRemove.parentElement.removeChild(divToRemove);
    }

    // Closing the overlay with click
    document.querySelector('body>.overlay').addEventListener('click', function (){
        removeOverlay();
    })

    // Closing the overlay with esc key
    document.addEventListener('keydown', function (e){
        if (e.key === 'Escape'){
            removeOverlay();
        }
    });

}

