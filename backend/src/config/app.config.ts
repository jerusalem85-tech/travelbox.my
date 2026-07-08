export const appConfig = () => ({
  port: parseInt(process.env.PORT || '3001', 10),
  jwt: {
    secret: process.env.JWT_SECRET || 'travelbox-jwt-secret-change-in-production',
    expiresIn: process.env.JWT_EXPIRATION || '1d',
    refreshExpiresIn: '7d',
  },
  database: {
    url: process.env.DATABASE_URL,
  },
});
