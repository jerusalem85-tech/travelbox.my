import type { Metadata } from "next";
import "./globals.css";

export const metadata: Metadata = {
  title: "TravelBox - نظام الإدارة والمحاسبة",
  description: "نظام إدارة ومحاسبة متكامل لوكالات السفر",
};

export default function RootLayout({
  children,
}: Readonly<{
  children: React.ReactNode;
}>) {
  return (
    <html lang="ar" dir="rtl">
      <body className="min-h-screen antialiased">{children}</body>
    </html>
  );
}
