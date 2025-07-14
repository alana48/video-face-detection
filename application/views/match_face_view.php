<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Face Matching</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script defer src="<?php echo base_url('assets/library/faceapi/dist/face-api.min.js'); ?>"></script>

  <!-- Optional favicon -->
  <link rel="icon" type="image/png" href="<?php echo base_url('assets/images/logo.png'); ?>" />
</head>
<body class="bg-gray-100 min-h-screen flex flex-col">

  <!-- NAVBAR -->
  <nav class="bg-indigo-700 text-white shadow w-full">
    <div class="max-w-7xl mx-auto px-4 py-4 flex justify-between items-center">
      <div class="text-lg font-bold">FaceAI System</div>
      <div class="space-x-6 text-sm">
        <a href="<?= base_url('face/register') ?>" class="hover:text-indigo-300">Register Face</a>
        <a href="<?= base_url('face/list_faces') ?>" class="hover:text-indigo-300">List Faces</a>
        <a href="<?= base_url('face/match_faces') ?>" class="hover:text-indigo-300 font-semibold underline">Match Faces</a>
        <a href="<?= base_url('face/index') ?>" class="hover:text-indigo-300">Video Detection</a>
      </div>
    </div>
  </nav>

  <!-- MAIN CONTENT -->
  <main class="flex-grow flex flex-col items-center py-6 px-4">
    <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-4xl">
      <h1 class="text-2xl font-bold text-indigo-600 mb-6 text-center">Face Matching</h1>

      <!-- WEBCAM -->
      <video id="webcam" autoplay muted playsinline
        class="w-full max-w-[900px] rounded shadow border mb-4"
        style="aspect-ratio: 4 / 3;"></video>

      <!-- STATUS -->
      <p id="matchStatus" class="text-center mb-4 text-base font-medium text-gray-700">Detecting face...</p>
    </div>
  </main>

  <!-- SCRIPT -->
  <script>
    const video = document.getElementById('webcam');
    const matchStatus = document.getElementById('matchStatus');

    async function fetchSavedFaces() {
      const response = await fetch('<?php echo base_url("face/get_all_faces"); ?>');
      const data = await response.json();

      return data.map(face => new faceapi.LabeledFaceDescriptors(
        face.name,
        [new Float32Array(JSON.parse(face.descriptor))]
      ));
    }

    async function setupCamera() {
      try {
        const stream = await navigator.mediaDevices.getUserMedia({ video: true });
        video.srcObject = stream;
        return new Promise(resolve => video.onloadedmetadata = resolve);
      } catch (err) {
        Swal.fire('Error', 'Unable to access webcam. Please check browser permission.', 'error');
        throw err;
      }
    }

    async function loadModels() {
      const MODEL_URL = '<?php echo base_url('assets/library/faceapi/weights'); ?>';
      await faceapi.nets.tinyFaceDetector.loadFromUri(MODEL_URL);
      await faceapi.nets.faceLandmark68Net.loadFromUri(MODEL_URL);
      await faceapi.nets.faceRecognitionNet.loadFromUri(MODEL_URL);
    }

    async function startRecognition() {
      const labeledDescriptors = await fetchSavedFaces();

      if (labeledDescriptors.length === 0) {
        matchStatus.textContent = 'No saved faces available for matching.';
        matchStatus.classList.add('text-red-600');
        return;
      }

      const faceMatcher = new faceapi.FaceMatcher(labeledDescriptors, 0.6);

      setInterval(async () => {
        const detection = await faceapi.detectSingleFace(video, new faceapi.TinyFaceDetectorOptions())
          .withFaceLandmarks()
          .withFaceDescriptor();

        if (!detection) {
          matchStatus.textContent = 'No face detected.';
          matchStatus.classList.remove('text-green-600');
          matchStatus.classList.add('text-red-600');
          return;
        }

        const bestMatch = faceMatcher.findBestMatch(detection.descriptor);

        if (bestMatch.label === 'unknown') {
          matchStatus.textContent = 'Face not recognized.';
          matchStatus.classList.remove('text-green-600');
          matchStatus.classList.add('text-red-600');
        } else {
          matchStatus.textContent = `Recognized: ${bestMatch.label} (distance: ${bestMatch.distance.toFixed(3)})`;
          matchStatus.classList.remove('text-red-600');
          matchStatus.classList.add('text-green-600');
        }
      }, 800);
    }

    window.onload = async () => {
      await loadModels();
      await setupCamera();
      matchStatus.textContent = 'Please look at the camera...';
      await startRecognition();
    };
  </script>

</body>
</html>
