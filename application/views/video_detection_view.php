<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Video Detection Face Recognition</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script defer src="<?php echo base_url('assets/library/faceapi/dist/face-api.min.js'); ?>"></script>
  <link rel="icon" type="image/png" href="<?php echo base_url('assets/images/logo.png'); ?>" />
  <style>
    /* Hilangkan kontrol seek video sepenuhnya dengan menyembunyikan UI default */
    video::-webkit-media-controls-timeline,
    video::-webkit-media-controls-seek-back-button,
    video::-webkit-media-controls-seek-forward-button {
      display: none !important;
    }
  </style>
</head>
<body class="bg-gray-100">

  <!-- NAVBAR -->
  <nav class="bg-indigo-700 text-white shadow w-full">
    <div class="max-w-7xl mx-auto px-4 py-4 flex justify-between items-center">
      <div class="text-lg font-bold">FaceAI System</div>
      <div class="space-x-6 text-sm">
        <a href="<?= base_url('face/register') ?>" class="hover:text-indigo-300">Regis Face</a>
        <a href="<?= base_url('face/list_faces') ?>" class="hover:text-indigo-300">List Face</a>
        <a href="<?= base_url('face/match_faces') ?>" class="hover:text-indigo-300">Match Faces</a>
        <a href="<?= base_url('face/index') ?>" class="hover:text-indigo-300 font-semibold underline">Video Detection</a>
      </div>
    </div>
  </nav>

  <div class="flex flex-col items-center justify-center py-6 px-4">

    <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-4xl">
      <h1 class="text-2xl font-bold text-center mb-6 text-indigo-600">Video Detection Face Recognition</h1>

      <!-- VIDEO -->
      <video id="videoPlayer" class="w-full max-w-[900px] mx-auto mb-6 rounded-lg shadow"
             disablePictureInPicture controlsList="nodownload nofullscreen noremoteplayback"
             oncontextmenu="return false" controls>
        <source src="<?php echo base_url('assets/videos/ai.mp4'); ?>" type="video/mp4">
        Browser Anda tidak mendukung pemutaran video.
      </video>

      <!-- STATUS LOG -->
      <div class="bg-gray-100 p-4 rounded mb-6 max-h-64 overflow-y-auto shadow-inner">
        <h2 class="font-semibold mb-2">Status Log:</h2>
        <pre id="logArea" class="text-sm font-mono text-gray-800"></pre>
      </div>

      <!-- WEBCAM -->
      <div>
        <video id="webcam" autoplay muted playsinline class="w-full max-w-[900px] rounded-lg shadow border"></video>
      </div>
    </div>
  </div>

<script>
  const webcam = document.getElementById('webcam');
  const videoPlayer = document.getElementById('videoPlayer');
  const logArea = document.getElementById('logArea');

  let lastAllowedTime = 0;
  let faceDetected = false;
  let swalShown = false;
  let allowSeek = false;
  let videoResumed = false;

  function log(message) {
    const now = new Date().toLocaleTimeString();
    logArea.textContent += `[${now}] ${message}\n`;
    logArea.scrollTop = logArea.scrollHeight;
  }

  async function setupWebcam() {
    try {
      const stream = await navigator.mediaDevices.getUserMedia({ video: true });
      webcam.srcObject = stream;
      log("Webcam aktif ✅");
      webcam.addEventListener('canplay', () => {
        log("Video siap, mulai deteksi wajah");
        startDetection();
      });
    } catch (err) {
      Swal.fire('Gagal', 'Tidak dapat mengakses webcam. Periksa izin browser.', 'error');
      console.error(err);
    }
  }

  async function loadModels() {
    const MODEL_URL = '<?php echo base_url('assets/library/faceapi/weights'); ?>';
    await faceapi.nets.tinyFaceDetector.loadFromUri(MODEL_URL);
    await faceapi.nets.faceLandmark68Net.loadFromUri(MODEL_URL);
    await faceapi.nets.faceRecognitionNet.loadFromUri(MODEL_URL);
    log("Model face-api berhasil dimuat ✅");
  }

  async function fetchSavedFaces() {
    const response = await fetch('<?php echo base_url("face/get_all_faces"); ?>');
    const data = await response.json();
    return data.map(face => {
      return new faceapi.LabeledFaceDescriptors(
        face.name,
        [new Float32Array(JSON.parse(face.descriptor))]
      );
    });
  }

  async function startDetection() {
    const labeledDescriptors = await fetchSavedFaces();
    const faceMatcher = new faceapi.FaceMatcher(labeledDescriptors, 0.6);
    const options = new faceapi.TinyFaceDetectorOptions({ inputSize: 224, scoreThreshold: 0.5 });

    setInterval(async () => {
      if (webcam.videoWidth > 0) {
        const detections = await faceapi.detectAllFaces(webcam, options)
          .withFaceLandmarks()
          .withFaceDescriptors();

        if (detections.length > 0) {
          const bestMatch = faceMatcher.findBestMatch(detections[0].descriptor);
          const isRecognized = bestMatch.label !== 'unknown';

          if (isRecognized) {
            faceDetected = true;
            lastAllowedTime = videoPlayer.currentTime;
            allowSeek = false;
            swalShown = false;

            if (!videoResumed) {
              Swal.fire({
                icon: 'success',
                title: 'Face Recognized',
                text: `Access granted for: ${bestMatch.label}`,
              });
              videoResumed = true;
            }

            log(`✅ Recognized: ${bestMatch.label} (distance: ${bestMatch.distance.toFixed(2)})`);
            videoPlayer.controls = true;

          } else {
            faceDetected = false;
            log(`❌ Face detected but not recognized`);
            if (!swalShown) {
              Swal.fire({
                icon: 'warning',
                title: 'Unknown Face',
                text: 'Face not recognized. Video is paused.',
              });
              swalShown = true;
            }
            videoPlayer.pause();
            videoPlayer.controls = false;
          }
        } else {
          faceDetected = false;
          log('❌ No face detected');
          if (!swalShown) {
            Swal.fire({
              icon: 'warning',
              title: 'No Face Detected',
              text: 'Please ensure your face is visible to resume the video.',
            });
            swalShown = true;
          }
          videoPlayer.pause();
          videoPlayer.controls = false;
        }
      }
    }, 1000);
  }


  videoPlayer.addEventListener('timeupdate', () => {
    lastAllowedTime = videoPlayer.currentTime;
  });

  videoPlayer.addEventListener('seeking', () => {
    if (!allowSeek && Math.abs(videoPlayer.currentTime - lastAllowedTime) > 0.5) {
      log('⚠️ Mencoba seek video - dicegah');
      videoPlayer.currentTime = lastAllowedTime;
      Swal.fire({
        icon: 'warning',
        title: 'Seek Dicegah',
        text: 'Anda tidak diperbolehkan menggeser video.',
      });
    }
  });

  window.onload = async () => {
    await loadModels();
    await setupWebcam();
  };
</script>

</body>
</html>
