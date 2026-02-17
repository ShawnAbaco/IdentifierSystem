<!DOCTYPE html>
<html>
<head>
    <title>Flower Identifier (Local Model)</title>
    <!-- TensorFlow.js and Teachable Machine -->
    <script src="https://cdn.jsdelivr.net/npm/@tensorflow/tfjs"></script>
<script src="https://cdn.jsdelivr.net/npm/@teachablemachine/image@0.8/dist/teachablemachine-image.min.js"></script>

    <style>
        #webcam-container { margin-top: 20px; }
        video { border: 1px solid #ccc; border-radius: 8px; }
        button { margin-right: 10px; padding: 8px 16px; font-size: 16px; }
        #result { margin-top: 15px; font-size: 20px; font-weight: bold; }
    </style>
</head>
<body>

<h2>Flower Identifier (Local Model)</h2>
<button id="start-button">Start Camera</button>
<button id="predict-button" disabled>Identify</button>

<div id="webcam-container"></div>
<h3 id="result"></h3>

<script>
const URL = "./model/"; // Make sure your model folder is in the same directory
let model, video, canvas, ctx;

window.onload = () => {
    document.getElementById("start-button").addEventListener("click", startCamera);
    document.getElementById("predict-button").addEventListener("click", predict);
}

async function startCamera() {
    const predictBtn = document.getElementById("predict-button");

    try {
        // Create video element
        if (!video) {
            video = document.createElement("video");
            video.width = 300;
            video.height = 300;
            video.autoplay = true;
            const container = document.getElementById("webcam-container");
            container.innerHTML = "";
            container.appendChild(video);
        }

        // Request webcam access
        const stream = await navigator.mediaDevices.getUserMedia({ video: true });
        video.srcObject = stream;

        // Play video (ignore minor autoplay errors)
        video.play().catch(err => console.warn("Autoplay prevented:", err));

        // Hidden canvas for predictions
        if (!canvas) {
            canvas = document.createElement("canvas");
            canvas.width = 300;
            canvas.height = 300;
            ctx = canvas.getContext("2d");
        }

        console.log("Camera started!");

        // Load Teachable Machine model
        if (!model) {
            if (typeof tmImage === "undefined") {
                alert("Error: Teachable Machine library did not load.");
                return;
            }
            console.log("Loading model...");
            model = await tmImage.load(URL + "model.json", URL + "metadata.json");
            console.log("Model loaded!");
        }

        // Enable predict button now that camera & model are ready
        predictBtn.disabled = false;
        console.log("Predict button enabled!");

    } catch (err) {
        console.error("Camera setup failed:", err);
        if (err.name === "NotAllowedError" || err.name === "NotFoundError") {
            alert("Camera setup failed. Make sure you allowed camera access and reload the page.");
        }
        predictBtn.disabled = true;
    }
}

async function predict() {
    if (!model || !video) {
        alert("Click Start Camera first!");
        return;
    }

    // Draw video frame on canvas
    ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

    // Run prediction
    const prediction = await model.predict(canvas);

    // Find the class with the highest probability
    let best = prediction[0];
    prediction.forEach(p => { if (p.probability > best.probability) best = p; });

    // Show result
    document.getElementById("result").innerText =
        best.className + " " + (best.probability * 100).toFixed(1) + "%";
}
</script>

</body>
</html>
