<!DOCTYPE html>
<html>
<body>

<button id="start">Start Camera</button>
<div id="video-container"></div>

<script>
document.getElementById("start").addEventListener("click", async () => {
    try {
        const stream = await navigator.mediaDevices.getUserMedia({ video: true });
        const video = document.createElement("video");
        video.srcObject = stream;
        video.width = 300;
        video.height = 300;
        video.autoplay = true;
        document.getElementById("video-container").appendChild(video);
        console.log("Camera started!");
    } catch (err) {
        console.error("Camera permission error:", err);
    }
});
</script>

</body>
</html>
