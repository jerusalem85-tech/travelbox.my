import type { Metadata } from "next";
import "./globals.css";

export const metadata: Metadata = {
  title: "TravelBox - Travel Agency ERP",
  description: "Integrated management and accounting system for travel agencies",
};

export default function RootLayout({
  children,
}: Readonly<{
  children: React.ReactNode;
}>) {
  return (
    <html lang="en" dir="ltr">
      <body className="min-h-screen antialiased">{children}</body>
    </html>
  );
}
