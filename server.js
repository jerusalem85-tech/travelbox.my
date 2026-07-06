const { spawn } = require('child_process');
const path = require('path');

const PORT = process.env.PORT || 3000;
const BACKEND_PORT = 3001;

console.log(`[TravelBox] Starting...`);

// Start NestJS backend on port 3001
const backend = spawn('node', ['dist/src/main'], {
  cwd: path.join(__dirname, 'backend'),
  env: { ...process.env, PORT: String(BACKEND_PORT), NODE_ENV: 'production' },
  stdio: 'pipe',
});
backend.stdout.on('data', (d) => process.stdout.write(`[API] ${d}`));
backend.stderr.on('data', (d) => process.stderr.write(`[API] ${d}`));

// Start Next.js frontend on PORT (standalone server deployed to frontend/)
const frontendDir = path.join(__dirname, 'frontend');
const frontend = spawn('node', ['server.js'], {
  cwd: frontendDir,
  env: { ...process.env, PORT: String(PORT), API_URL: `http://localhost:${BACKEND_PORT}` },
  stdio: 'pipe',
});
frontend.stdout.on('data', (d) => process.stdout.write(`[Web] ${d}`));
frontend.stderr.on('data', (d) => process.stderr.write(`[Web] ${d}`));

process.on('SIGTERM', () => { backend.kill(); frontend.kill(); process.exit(); });
process.on('SIGINT', () => { backend.kill(); frontend.kill(); process.exit(); });
