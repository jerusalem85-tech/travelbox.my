import { PrismaClient } from '@prisma/client';
import * as bcrypt from 'bcryptjs';

const prisma = new PrismaClient();

async function main() {
  const adminExists = await prisma.user.findUnique({ where: { email: 'admin@travelbox.my' } });
  if (adminExists) {
    console.log('Admin user already exists.');
    return;
  }

  await prisma.user.create({
    data: {
      email: 'admin@travelbox.my',
      password: await bcrypt.hash('admin123', 10),
      firstName: 'Admin',
      lastName: 'TravelBox',
      role: 'OWNER',
      isActive: true,
    },
  });
  console.log('Admin user created: admin@travelbox.my / admin123');
}

main().catch(console.error).finally(() => prisma.$disconnect());
