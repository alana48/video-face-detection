<!-- application/views/register_face.php -->
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Face Registration</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script defer src="<?php echo base_url('assets/library/faceapi/dist/face-api.min.js'); ?>"></script>
  <link rel="icon" type="image/png" href="<?php echo base_url('assets/images/logo.png'); ?>" />
</head>
<body class="bg-gray-100">

  <!-- NAVBAR -->
  <nav class="bg-indigo-700 text-white shadow w-full">
    <div class="max-w-7xl mx-auto px-4 py-4 flex justify-between items-center">
      <div class="text-lg font-bold">FaceAI System</div>
      <div class="space-x-6 text-sm">
        <a href="<?= base_url('face/register') ?>" class="hover:text-indigo-300 font-semibold underline">Register Face</a>
        <a href="<?= base_url('face/list_faces') ?>" class="hover:text-indigo-300">List Faces</a>
        <a href="<?= base_url('face/match_faces') ?>" class="hover:text-indigo-300">Match Faces</a>
        <a href="<?= base_url('face/') ?>" class="hover:text-indigo-300">Video Detection</a>
      </div>
    </div>
  </nav>

  <!-- CONTENT -->
  <div class="flex flex-col items-center justify-center py-6 px-4">
    <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-4xl">

      <h1 class="text-2xl font-bold text-center mb-6 text-indigo-600">Face Registration</h1>

      <!-- WEBCAM -->
      <video id="webcam" autoplay muted playsinline class="w-full max-w-[900px] mx-auto mb-4 rounded shadow border" style="aspect-ratio: 4 / 3;"></video>
      <p id="faceStatus" class="text-center mb-4 text-sm text-gray-700 font-medium">Detecting face...</p>

      <!-- FORM -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
        <div>
          <label class="block text-sm mb-1">Upload face photo (optional):</label>
          <input type="file" id="photoInput" accept="image/*" class="w-full border border-gray-300 rounded p-2" />
        </div>
        <div>
          <label class="block text-sm mb-1">Name:</label>
          <input type="text" id="inputName" placeholder="Enter Name"
            class="w-full p-2 border border-gray-300 rounded focus:outline-indigo-500" />
        </div>
      </div>

      <button id="btnRegister"
        class="w-full bg-indigo-600 text-white py-3 rounded hover:bg-indigo-700 transition">Register Face</button>
    </div>
  </div>

  <script>
    const video = document.getElementById('webcam');
    const inputName = document.getElementById('inputName');
    const btnRegister = document.getElementById('btnRegister');
    const photoInput = document.getElementById('photoInput');
    const faceStatus = document.getElementById('faceStatus');

    let useUpload = false;

    async function setupCamera() {
      try {
        const stream = await navigator.mediaDevices.getUserMedia({ video: true });
        video.srcObject = stream;
        return new Promise(resolve => video.onloadedmetadata = () => resolve());
      } catch (err) {
        Swal.fire('Error', 'Cannot access webcam.', 'error');
        throw err;
      }
    }

    async function loadModels() {
      const MODEL_URL = '<?php echo base_url('assets/library/faceapi/weights'); ?>';
      await faceapi.nets.tinyFaceDetector.loadFromUri(MODEL_URL);
      await faceapi.nets.faceLandmark68Net.loadFromUri(MODEL_URL);
      await faceapi.nets.faceRecognitionNet.loadFromUri(MODEL_URL);
    }

    async function detectFaceStatus() {
      if (useUpload) return;
      const detection = await faceapi.detectSingleFace(video, new faceapi.TinyFaceDetectorOptions());
      if (detection) {
        faceStatus.textContent = `Face detected with confidence ${(detection.score * 100).toFixed(2)}%`;
        faceStatus.classList.remove('text-red-600');
        faceStatus.classList.add('text-green-600');
      } else {
        faceStatus.textContent = 'No face detected';
        faceStatus.classList.remove('text-green-600');
        faceStatus.classList.add('text-red-600');
      }
    }

    async function captureFromVideo() {
      const detection = await faceapi.detectSingleFace(video, new faceapi.TinyFaceDetectorOptions())
        .withFaceLandmarks()
        .withFaceDescriptor();

      if (!detection) return null;

      const descriptor = detection.descriptor;

      const canvas = document.createElement('canvas');
      canvas.width = video.videoWidth;
      canvas.height = video.videoHeight;
      const ctx = canvas.getContext('2d');
      ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
      const photoBase64 = canvas.toDataURL('image/jpeg').split(',')[1];

      return { descriptor: Array.from(descriptor), photoBase64 };
    }

    async function captureFromUpload(file) {
      const img = await faceapi.bufferToImage(file);
      const detection = await faceapi.detectSingleFace(img, new faceapi.TinyFaceDetectorOptions())
        .withFaceLandmarks()
        .withFaceDescriptor();

      if (!detection) return null;

      const descriptor = detection.descriptor;

      const canvasExport = document.createElement('canvas');
      canvasExport.width = img.width;
      canvasExport.height = img.height;
      const ctx = canvasExport.getContext('2d');
      ctx.drawImage(img, 0, 0);
      const photoBase64 = canvasExport.toDataURL('image/jpeg').split(',')[1];

      return { descriptor: Array.from(descriptor), photoBase64 };
    }

    btnRegister.addEventListener('click', async () => {
      const name = inputName.value.trim();
      if (!name) return Swal.fire('Error', 'Name is required', 'error');

      btnRegister.disabled = true;
      btnRegister.textContent = 'Saving...';

      try {
        let data;
        if (useUpload && photoInput.files[0]) {
          data = await captureFromUpload(photoInput.files[0]);
        } else {
          data = await captureFromVideo();
        }

        if (!data) return Swal.fire('Error', 'Face not detected', 'error');

        const response = await fetch('<?php echo base_url("face/save"); ?>', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({
            name: name,
            descriptor: data.descriptor,
            photo_base64: data.photoBase64,
          }),
        });

        const result = await response.json();
        if (result.status === 'success') {
          Swal.fire('Success', 'Face saved successfully', 'success');
          inputName.value = '';
          photoInput.value = '';
          useUpload = false;
        } else {
          Swal.fire('Failed', result.message || 'An error occurred', 'error');
        }
      } catch (e) {
        Swal.fire('Error', 'Failed to save data: ' + e.message, 'error');
      } finally {
        btnRegister.disabled = false;
        btnRegister.textContent = 'Register Face';
      }
    });

    photoInput.addEventListener('change', () => {
      if (photoInput.files.length > 0) {
        useUpload = true;
        faceStatus.textContent = 'Image ready for detection.';
        faceStatus.classList.remove('text-red-600');
        faceStatus.classList.add('text-yellow-600');
      } else {
        useUpload = false;
      }
    });

    window.onload = async () => {
      await loadModels();
      await setupCamera();
      setInterval(detectFaceStatus, 500);
    };
  </script>
</body>
</html>
