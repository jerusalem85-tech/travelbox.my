/** @type {import('next').NextConfig} */
const nextConfig = {
  images: { domains: ['localhost', 'travelbox.my'] },
  env: {
    NEXT_PUBLIC_API_URL: process.env.NEXT_PUBLIC_API_URL || 'http://localhost:3001',
    NEXT_PUBLIC_APP_NAME: 'TravelBox ERP',
  },
};

module.exports = nextConfig;
