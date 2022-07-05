const player = document.querySelector('.player'),
    playBtn = document.querySelector('.play'),
    prevBtn = document.querySelector('.prev'),
    nextBtn = document.querySelector('.next'),
    audio = document.querySelector('.audio'),
    progressContainer = document.querySelector('.progress_container'),
    progress = document.querySelector('.progress'),
    title = document.querySelector('.song'),
    imgSrc = document.querySelector('.img__src'),
    audioId = document.querySelector('.audioId'),
    muzlo = document.querySelectorAll('.muzlo'),
    duration = document.querySelectorAll('.duration')
playOnTrackBtn = document.querySelector('.playOnTrack')

player.style.visibility = 'hidden';


// Названия песен
//const songs = []

// Песня по умолчанию
//let songIndex = 0

// Init
//function loadSong(song) {
//    title.innerHTML = song
//    audio.src = `/audio/${song}.mp3`
//}

//loadSong(songs[songIndex])

// Play
function playSong() {

    player.classList.add('play')
    imgSrc.src = '/images/playerImages/pause.jpg'
    audio.play()
}

// Pause
function pauseSong() {
    player.classList.remove('play')
    imgSrc.src = '/images/playerImages/play.jpg'
    audio.pause()
    // highlightedItems.forEach(function (Element){
    //     Element.children[0].children[5].src = ''
    // })
}

playBtn.addEventListener('click', () => {
    const isPlaying = player.classList.contains('play')
    if (isPlaying) {
        pauseSong()
    } else {
        playSong()
    }
})

// Next song
//function nextSong() {
//    songIndex++

//    if (songIndex > songs.length - 1) {
//        songIndex = 0
//    }

//    loadSong(songs[songIndex])
//    playSong()
//}

//nextBtn.addEventListener('click', () => {
//    nextSong()
//})

// Prev song
//function prevSong() {
//    songIndex--
//   if (songIndex < 0) {
//        songIndex = songs.length - 1
//    }
//    loadSong(songs[songIndex])
//    playSong()
//}

//prevBtn.addEventListener('click', () => {
//    prevSong()
//})

// Progress bar
function updateProgress(e) {
    const {duration, currentTime} = e.target
    const progressPercent = (currentTime / duration) * 100
    progress.style.width = `${progressPercent}%`
}

audio.addEventListener('timeupdate', updateProgress)

//Set progress
function setProgress(e) {
    const width = this.clientWidth
    const clickX = e.offsetX
    const duration = audio.duration

    audio.currentTime = (clickX / width) * duration
}

progressContainer.addEventListener('click', setProgress)

//Autoplay
//audio.addEventListener('ended', nextSong)


// Clicked on song
//________________________________________________________________________________________________________________
var highlightedItems = document.querySelectorAll(".pesnya");
const progressTime = document.querySelector('.progressTime')
songDurationOnPlayer = document.querySelector('.songDurationOnPlayer')

highlightedItems.forEach(function (Element) {
    var audioFromList = Element.children[0].children[3].innerHTML;
    var currentArrayIndex = Element.children[0].children[0].innerHTML - 1;
    var lengthOfAlbum = Element.children[0].children[7].innerHTML
    var songsFromOneAlbum = document.querySelectorAll(`[id^="${Element.children[0].children[8].innerHTML}"]`)

    //download song
    var songName = Element.children[0].children[3].innerHTML
    Element.children[0].children[9].children[0].href = `/audio/${songName}.mp3`

    //Clicked
    Element.children[0].children[1].addEventListener('click', () => {

        //other than clicked to be dynamic during mouseOn
        highlightedItems.forEach(function (Element) {
            Element.classList = 'pesnya'
            Element.children[0].children[1].children[0].src = '/images/playerImages/pngwing.com.png'
            Element.children[0].children[2].classList = 'nowPlaying';

        })

        audio.addEventListener('ended', () => {
            if ((lengthOfAlbum - 1) < (currentArrayIndex + 1)) {
                songsFromOneAlbum[0].children[1].click()
            } else {
                songsFromOneAlbum[currentArrayIndex + 1].children[1].click()
            }
        })

        //next song
        nextBtn.addEventListener('click', () => {
            if ((lengthOfAlbum - 1) < (currentArrayIndex + 1)) {
                songsFromOneAlbum[0].children[1].click()
            } else {
                songsFromOneAlbum[currentArrayIndex + 1].children[1].click()
            }
        })
        //preveous song
        prevBtn.addEventListener('click', () => {
            if (!songsFromOneAlbum[currentArrayIndex - 1]) {
                songsFromOneAlbum[0].children[1].click()
            } else {
                songsFromOneAlbum[currentArrayIndex - 1].children[1].click()
            }
        })

        //stop and resume playing song
        Element.children[0].children[2].classList = 'nowPlaying1';
        playBtn.addEventListener('click', () => {
            const isPlaying = player.classList.contains('play')
            if (isPlaying) {
                highlightedItems.forEach(function (Element) {
                    Element.children[0].children[2].classList = 'nowPlaying';
                })
                Element.children[0].children[2].classList = 'nowPlaying1';
            } else {

                Element.children[0].children[2].classList = 'nowPlaying';
            }
        })

        //play handling
        Element.classList = 'pesnya2'
        title.innerHTML = audioFromList
        audio.src = `/audio/${audioFromList}.mp3`
        player.style.visibility = 'visible';
        imgSrc.src = '/images/playerImages/pause.jpg'
        audio.play()
        player.classList.add('play')

        var albumImage = Element.children[0].children[6].innerHTML
        var playerImage = document.querySelector('.playerImage')

        playerImage.src = `/images/albumsImages/${albumImage}`

        // progress on player
        audio.addEventListener('timeupdate', () => {
            var minutesDuration = Math.trunc(audio.duration / 60)
            var secondsDuration = Math.trunc(((audio.duration / 60) - Math.trunc(audio.duration / 60)) * 60)
            var minutes = Math.trunc(audio.currentTime / 60)
            var seconds = Math.trunc(((audio.currentTime / 60) - Math.trunc(audio.currentTime / 60)) * 60)

            if (seconds < 10) {
                progressTime.innerHTML ='0'+ minutes +':0' + seconds
            } else if (seconds >= 10) {
                progressTime.innerHTML ='0'+ minutes +':' + seconds
            } else if (minutes >= 10 && seconds < 10) {
                progressTime.innerHTML = minutes +':0' + seconds
            } else if (minutes >= 10 && seconds >= 10) {
                progressTime.innerHTML =minutes +':' + seconds
            }

            if (secondsDuration < 10) {
                songDurationOnPlayer.innerHTML ='0'+ minutesDuration +':0' + secondsDuration
            } else if (secondsDuration >= 10) {
                songDurationOnPlayer.innerHTML ='0'+ minutesDuration +':' + secondsDuration
            } else if (minutesDuration >= 10 && secondsDuration < 10) {
                songDurationOnPlayer.innerHTML = minutesDuration +':0' + secondsDuration
            } else if (minutesDuration >= 10 && secondsDuration >= 10) {
                songDurationOnPlayer.innerHTML =minutesDuration +':' + secondsDuration
            }

        })
    })

});
//________________________________________________________________________________________________________________



