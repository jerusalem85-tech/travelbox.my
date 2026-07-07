import tus from 'tus-js-client';
import fs from 'fs';
import path from 'path';

const filePath = path.resolve('./deploy.zip');
const fileStream = fs.createReadStream(filePath);
const stats = fs.statSync(filePath);

const uploadUrl = 'https://srv2069-files.hstgr.io/rest/2e6c999960676954/api/tus/public_html';
const uploadPath = `${uploadUrl}/deploy.zip?override=true`;
const authKey = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c2VyIjp7ImlkIjoxLCJsb2NhbGUiOiJlbl9VUyIsInZpZXdNb2RlIjoibGlzdCIsInNpbmdsZUNsaWNrIjpmYWxzZSwicmVkaXJlY3RBZnRlckNvcHlNb3ZlIjpmYWxzZSwicGVybSI6eyJhZG1pbiI6ZmFsc2UsImV4ZWN1dGUiOmZhbHNlLCJjcmVhdGUiOnRydWUsInJlbmFtZSI6dHJ1ZSwibW9kaWZ5Ijp0cnVlLCJkZWxldGUiOnRydWUsInNoYXJlIjpmYWxzZSwiZG93bmxvYWQiOnRydWV9LCJjb21tYW5kcyI6W10sImxvY2tQYXNzd29yZCI6dHJ1ZSwiaGlkZURvdGZpbGVzIjpmYWxzZSwiZGF0ZUZvcm1hdCI6ZmFsc2UsInVzZXJuYW1lIjoidTkwODM3MjMyOSIsImFjZUVkaXRvclRoZW1lIjoiIn0sImlzcyI6IkZpbGUgQnJvd3NlciIsImV4cCI6MTc4MzQ1NTIxOSwiaWF0IjoxNzgzNDMzNjE5fQ.P1luYULLZIt3CwlI7jsIEgxX_kR4WMb7ZwMGMQiafyk';
const restAuth = 'cd32960a2d7659cde73ed6a1f2bbbdcb03dcfefd0e1e289cf53dfa99d57b5ab0-2e6c999960676954';

try {
  // Initiate TUS upload
  const cleanUrl = uploadUrl.replace(/\/$/, '');
  const uploadUrlWithFile = `${cleanUrl}/deploy.zip?override=true`;

  console.log('Uploading to:', uploadUrlWithFile);
  console.log('File size:', stats.size);

  const upload = new tus.Upload(fileStream, {
    uploadUrl: uploadUrlWithFile,
    retryDelays: [1000, 2000, 4000, 8000, 16000, 20000],
    uploadDataDuringCreation: false,
    parallelUploads: 1,
    chunkSize: 10485760,
    headers: {
      'X-Auth': authKey,
      'X-Auth-Rest': restAuth,
      'upload-length': stats.size.toString(),
      'upload-offset': '0'
    },
    removeFingerprintOnSuccess: true,
    uploadSize: stats.size,
    metadata: {
      filename: 'deploy.zip'
    },
    onError: (error) => {
      console.error('Upload error:', error);
      process.exit(1);
    },
    onProgress: (bytesUploaded, bytesTotal) => {
      console.log(`Progress: ${bytesUploaded}/${bytesTotal} (${Math.round(bytesUploaded/bytesTotal*100)}%)`);
    },
    onSuccess: () => {
      console.log('Upload completed successfully!');
      process.exit(0);
    }
  });

  upload.start();
} catch (err) {
  console.error('Error:', err);
  process.exit(1);
}
