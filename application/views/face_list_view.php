<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Saved Faces List</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="icon" type="image/png" href="<?php echo base_url('assets/images/logo.png'); ?>" />
</head>
<body class="bg-gray-100 min-h-screen flex flex-col">

  <!-- NAVBAR -->
  <nav class="bg-indigo-700 text-white shadow w-full">
    <div class="max-w-7xl mx-auto px-4 py-4 flex justify-between items-center">
      <div class="text-lg font-bold">FaceAI System</div>
      <div class="space-x-6 text-sm">
        <a href="<?= base_url('face/register') ?>" class="hover:text-indigo-300">Register Face</a>
        <a href="<?= base_url('face/list_faces') ?>" class="hover:text-indigo-300 font-semibold underline">List Faces</a>
        <a href="<?= base_url('face/match_faces') ?>" class="hover:text-indigo-300">Match Faces</a>
        <a href="<?= base_url('face/index') ?>" class="hover:text-indigo-300">Video Detection</a>
      </div>
    </div>
  </nav>

  <!-- MAIN CONTENT -->
  <main class="flex-grow flex flex-col items-center py-6 px-4">
    <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-4xl">
      <h1 class="text-2xl font-bold text-indigo-600 mb-6 text-center">Saved Faces List</h1>

      <div class="overflow-x-auto">
        <table class="min-w-full border border-gray-300 rounded-lg divide-y divide-gray-200">
          <thead>
            <tr class="bg-indigo-100 text-indigo-800 uppercase text-sm font-semibold">
              <th class="border border-gray-300 px-6 py-3 text-left">ID</th>
              <th class="border border-gray-300 px-6 py-3 text-left">Name</th>
              <th class="border border-gray-300 px-6 py-3 text-center">Photo</th>
              <th class="border border-gray-300 px-6 py-3 text-left max-w-[400px]">Descriptor (JSON)</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($faces)) : ?>
              <?php foreach ($faces as $face) : ?>
                <tr class="hover:bg-indigo-50 transition-colors duration-150 cursor-pointer">
                  <td class="border border-gray-300 px-6 py-3 text-gray-700 font-medium"><?php echo $face->id; ?></td>
                  <td class="border border-gray-300 px-6 py-3 text-gray-800"><?php echo htmlspecialchars($face->name); ?></td>
                  <td class="border border-gray-300 px-6 py-3 text-center">
                    <?php if (!empty($face->photo_base64)) : ?>
                      <img
                        src="data:image/jpeg;base64,<?php echo $face->photo_base64; ?>"
                        alt="Face Photo"
                        class="mx-auto w-16 h-16 object-cover rounded-full border border-indigo-300 shadow-sm"
                        loading="lazy"
                      />
                    <?php else : ?>
                      <span class="text-gray-400 italic">No photo available</span>
                    <?php endif; ?>
                  </td>
                  <td class="border border-gray-300 px-6 py-3 max-w-[400px] whitespace-pre-wrap break-words text-xs font-mono text-gray-600 overflow-auto" style="max-height: 8rem;">
                    <?php echo htmlspecialchars($face->descriptor); ?>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else : ?>
              <tr>
                <td class="border border-gray-300 px-6 py-4 text-center text-gray-500 italic" colspan="4">
                  No saved face data available.
                </td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </main>

</body>
</html>
