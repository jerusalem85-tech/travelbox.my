"use client";

import { useEffect } from "react";
import { useRouter } from "next/navigation";

export default function Home() {
  const router = useRouter();

  useEffect(() => {
    const token = localStorage.getItem("travelbox_token");
    router.push(token ? "/dashboard" : "/login");
  }, [router]);

  return (
    <div className="min-h-screen bg-gradient-to-br from-blue-600 to-blue-800 flex items-center justify-center">
      <div className="animate-spin w-10 h-10 border-4 border-white border-t-transparent rounded-full" />
    </div>
  );
}
