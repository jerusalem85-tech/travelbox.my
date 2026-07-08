"use client";

import Link from "next/link";
import { usePathname, useRouter } from "next/navigation";
import {
  LayoutDashboard, Plane, Users, Building2, FileText, BarChart3,
  Settings, LogOut, ChevronDown, Wallet, Receipt,
} from "lucide-react";
import { useState } from "react";

const navItems = [
  { href: "/dashboard", label: "Dashboard", icon: LayoutDashboard },
  {
    label: "Trips", icon: Plane, children: [
      { href: "/trips", label: "All Trips" },
      { href: "/trips/new", label: "New Trip" },
    ],
  },
  { href: "/customers", label: "Customers", icon: Users },
  { href: "/suppliers", label: "Suppliers", icon: Building2 },
  {
    label: "Finance", icon: Wallet, children: [
      { href: "/payments", label: "Payments" },
      { href: "/invoices", label: "Invoices" },
    ],
  },
  {
    label: "Accounting", icon: BarChart3, children: [
      { href: "/accounting", label: "Journal" },
      { href: "/accounting/trial-balance", label: "Trial Balance" },
      { href: "/accounting/profit-loss", label: "Profit & Loss" },
    ],
  },
  {
    label: "Documents", icon: FileText, children: [
      { href: "/documents", label: "All Documents" },
      { href: "/documents/generate", label: "New Document" },
    ],
  },
];

export default function DashboardLayout({ children }: { children: React.ReactNode }) {
  const pathname = usePathname();
  const router = useRouter();
  const [expanded, setExpanded] = useState<string | null>(null);

  const handleLogout = () => {
    localStorage.removeItem("travelbox_token");
    localStorage.removeItem("travelbox_user");
    router.push("/login");
  };

  return (
    <div className="min-h-screen flex">
      <aside className="w-64 bg-[#0f172a] text-white flex flex-col fixed h-full z-50">
        <div className="p-5 border-b border-white/10">
          <Link href="/dashboard" className="flex items-center gap-3">
            <div className="w-9 h-9 bg-blue-600 rounded-lg flex items-center justify-center">
              <Plane className="w-5 h-5 text-white" />
            </div>
            <div>
              <h1 className="text-lg font-bold">TravelBox</h1>
              <p className="text-xs text-blue-300">ERP System</p>
            </div>
          </Link>
        </div>

        <nav className="flex-1 overflow-y-auto p-3 space-y-1">
          {navItems.map((item) => {
            if ("children" in item && item.children) {
              const childActive = item.children.some((c) => pathname.startsWith(c.href));
              const isExpanded = expanded === item.label || childActive;
              const isActive = childActive;
              return (
                <div key={item.label}>
                  <button
                    onClick={() => setExpanded(isExpanded ? null : item.label)}
                    className={`w-full flex items-center justify-between px-3 py-2.5 rounded-lg text-sm transition-colors ${
                      isActive ? "bg-blue-600/20 text-blue-300" : "text-gray-300 hover:bg-white/5"
                    }`}
                  >
                    <div className="flex items-center gap-3">
                      <item.icon className="w-4 h-4" />
                      <span>{item.label}</span>
                    </div>
                    <ChevronDown className={`w-3.5 h-3.5 transition-transform ${isExpanded ? "rotate-180" : ""}`} />
                  </button>
                  {isExpanded && (
                    <div className="ml-4 mt-1 space-y-1">
                      {item.children.map((child) => (
                        <Link
                          key={child.href}
                          href={child.href}
                          className={`block px-3 py-2 rounded-lg text-sm transition-colors ${
                            pathname === child.href ? "bg-blue-600/30 text-blue-300" : "text-gray-400 hover:text-white hover:bg-white/5"
                          }`}
                        >
                          {child.label}
                        </Link>
                      ))}
                    </div>
                  )}
                </div>
              );
            }
            const Icon = item.icon!;
            const isActive = pathname.startsWith(item.href);
            return (
              <Link
                key={item.href}
                href={item.href}
                className={`flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors ${
                  isActive ? "bg-blue-600/20 text-blue-300" : "text-gray-300 hover:bg-white/5"
                }`}
              >
                <Icon className="w-4 h-4" />
                <span>{item.label}</span>
              </Link>
            );
          })}
        </nav>

        <div className="p-3 border-t border-white/10">
          <button
            onClick={handleLogout}
            className="flex items-center gap-3 w-full px-3 py-2.5 rounded-lg text-sm text-gray-400 hover:text-white hover:bg-white/5 transition-colors"
          >
            <LogOut className="w-4 h-4" />
            <span>Sign Out</span>
          </button>
        </div>
      </aside>

      <main className="ml-64 flex-1 p-8">{children}</main>
    </div>
  );
}
