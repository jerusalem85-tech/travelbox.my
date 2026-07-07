"use client";

import { useState } from "react";
import { useRouter } from "next/navigation";
import { Plane, Eye, EyeOff } from "lucide-react";
import { api } from "@/lib/api";

export default function LoginPage() {
  const router = useRouter();
  const [email, setEmail] = useState("admin@travelbox.my");
  const [password, setPassword] = useState("admin123");
  const [showPassword, setShowPassword] = useState(false);
  const [error, setError] = useState("");
  const [loading, setLoading] = useState(false);

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setError("");
    setLoading(true);
    try {
      const res = await api.auth.login(email, password);
      localStorage.setItem("travelbox_token", res.accessToken);
      localStorage.setItem("travelbox_user", JSON.stringify(res.user));
      router.push("/dashboard");
    } catch (err: unknown) {
      setError(err instanceof Error ? err.message : "فشل تسجيل الدخول");
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="min-h-screen bg-gradient-to-br from-blue-600 to-blue-800 flex items-center justify-center p-4">
      <div className="w-full max-w-md">
        <div className="text-center mb-8">
          <div className="inline-flex items-center justify-center w-16 h-16 bg-white/20 rounded-2xl mb-4">
            <Plane className="w-8 h-8 text-white" />
          </div>
          <h1 className="text-3xl font-bold text-white">TravelBox</h1>
          <p className="text-blue-200 mt-2">نظام إدارة وكالات السفر</p>
        </div>

        <div className="bg-white rounded-2xl p-8 shadow-xl">
          <h2 className="text-xl font-semibold text-gray-900 mb-6 text-center">
            تسجيل الدخول
          </h2>

          {error && (
            <div className="mb-4 p-3 bg-red-50 border border-red-200 text-red-700 text-sm rounded-xl">
              {error}
            </div>
          )}

          <form onSubmit={handleSubmit} className="space-y-4">
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">
                البريد الإلكتروني
              </label>
              <input
                type="email"
                value={email}
                onChange={(e) => setEmail(e.target.value)}
                className="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                required
                dir="ltr"
              />
            </div>

            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">
                كلمة المرور
              </label>
              <div className="relative">
                <input
                  type={showPassword ? "text" : "password"}
                  value={password}
                  onChange={(e) => setPassword(e.target.value)}
                  className="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  required
                  dir="ltr"
                />
                <button
                  type="button"
                  onClick={() => setShowPassword(!showPassword)}
                  className="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600"
                >
                  {showPassword ? <EyeOff className="w-4 h-4" /> : <Eye className="w-4 h-4" />}
                </button>
              </div>
            </div>

            <button
              type="submit"
              disabled={loading}
              className="w-full py-2.5 bg-blue-600 hover:bg-blue-700 disabled:opacity-50 text-white font-medium rounded-xl transition-colors"
            >
              {loading ? "جاري تسجيل الدخول..." : "تسجيل الدخول"}
            </button>
          </form>
        </div>
      </div>
    </div>
  );
}
