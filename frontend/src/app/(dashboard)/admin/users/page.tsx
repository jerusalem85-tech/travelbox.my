'use client';

import { useEffect, useState } from 'react';
import { useRouter } from 'next/navigation';
import { Card, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { api } from '@/lib/api';

interface User {
  id: string; firstName: string; lastName: string; email: string; role: string; isActive: boolean; createdAt: string;
}

export default function UsersPage() {
  const router = useRouter();
  const [users, setUsers] = useState<User[]>([]);
  const [showForm, setShowForm] = useState(false);
  const [form, setForm] = useState({ firstName: '', lastName: '', email: '', password: '', role: 'SALES' });

  useEffect(() => {
    if (!localStorage.getItem('token')) return void router.push('/login');
    api.get('/users').then(setUsers).catch(() => router.push('/login'));
  }, [router]);

  async function createUser() {
    await api.post('/users', form);
    setShowForm(false);
    setForm({ firstName: '', lastName: '', email: '', password: '', role: 'SALES' });
    api.get('/users').then(setUsers);
  }

  async function toggleActive(user: User) {
    await api.put(`/users/${user.id}`, { isActive: !user.isActive });
    api.get('/users').then(setUsers);
  }

  return (
    <div className="space-y-6">
      <div className="flex items-center justify-between">
        <h1 className="text-2xl font-bold">Users</h1>
        <Button onClick={() => setShowForm(!showForm)}>{showForm ? 'Cancel' : 'Add User'}</Button>
      </div>

      {showForm && (
        <Card className="max-w-md space-y-4">
          <CardTitle>New User</CardTitle>
          <Input label="First Name" value={form.firstName} onChange={(e) => setForm({ ...form, firstName: e.target.value })} />
          <Input label="Last Name" value={form.lastName} onChange={(e) => setForm({ ...form, lastName: e.target.value })} />
          <Input label="Email" type="email" value={form.email} onChange={(e) => setForm({ ...form, email: e.target.value })} />
          <Input label="Password" type="password" value={form.password} onChange={(e) => setForm({ ...form, password: e.target.value })} />
          <select className="block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm" value={form.role} onChange={(e) => setForm({ ...form, role: e.target.value })}>
            <option value="SALES">Sales</option>
            <option value="OPERATIONS">Operations</option>
            <option value="ACCOUNTING">Accounting</option>
            <option value="MANAGER">Manager</option>
            <option value="OWNER">Owner</option>
          </select>
          <Button onClick={createUser}>Create</Button>
        </Card>
      )}

      <Card>
        <div className="overflow-x-auto">
          <table className="w-full text-sm">
            <thead>
              <tr className="border-b text-left">
                <th className="pb-3 font-medium">Name</th>
                <th className="pb-3 font-medium">Email</th>
                <th className="pb-3 font-medium">Role</th>
                <th className="pb-3 font-medium">Status</th>
                <th className="pb-3 font-medium">Actions</th>
              </tr>
            </thead>
            <tbody>
              {users.map((u) => (
                <tr key={u.id} className="border-b last:border-0">
                  <td className="py-3">{u.firstName} {u.lastName}</td>
                  <td className="py-3 text-gray-600">{u.email}</td>
                  <td className="py-3"><span className="px-2 py-0.5 rounded bg-gray-100 text-xs">{u.role}</span></td>
                  <td className="py-3">
                    <span className={`px-2 py-0.5 rounded text-xs ${u.isActive ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'}`}>
                      {u.isActive ? 'Active' : 'Disabled'}
                    </span>
                  </td>
                  <td className="py-3">
                    <Button variant={u.isActive ? 'danger' : 'secondary'} size="sm" onClick={() => toggleActive(u)}>
                      {u.isActive ? 'Disable' : 'Enable'}
                    </Button>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      </Card>
    </div>
  );
}
