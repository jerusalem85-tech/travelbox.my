'use client';

import { useState } from 'react';
import { Card } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Button } from '@/components/ui/button';
import { Search, Plane, Building2, FileText, Users } from 'lucide-react';
import api from '@/lib/api';

export default function SearchPage() {
  const [query, setQuery] = useState('');
  const [results, setResults] = useState<any>(null);
  const [loading, setLoading] = useState(false);

  const handleSearch = async () => {
    if (!query.trim()) return;
    setLoading(true);
    try {
      const { data } = await api.get('/api/search', { params: { q: query } });
      setResults(data);
    } catch { setResults(null) }
    finally { setLoading(false) }
  };

  return (
    <div className="space-y-6">
      <div><h1 className="text-2xl font-bold">Search</h1><p className="text-muted-foreground">Search across all modules</p></div>
      <Card className="p-4">
        <div className="flex gap-2">
          <div className="relative flex-1">
            <Search className="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" />
            <Input placeholder="Search trips, customers, suppliers, documents..." value={query} onChange={e => setQuery(e.target.value)} onKeyDown={e => e.key === 'Enter' && handleSearch()} className="pl-9" />
          </div>
          <Button onClick={handleSearch} disabled={loading}>{loading ? 'Searching...' : 'Search'}</Button>
        </div>
      </Card>

      {results && (
        <div className="space-y-4">
          {results.trips?.length > 0 && (
            <Card className="p-4">
              <h3 className="text-sm font-medium mb-3 flex items-center gap-2"><Plane className="h-4 w-4" /> Trips ({results.trips.length})</h3>
              <div className="space-y-2">
                {results.trips.map((t: any) => (
                  <div key={t.id} className="text-sm py-1"><span className="font-medium">{t.tripNumber}</span> - {t.name}</div>
                ))}
              </div>
            </Card>
          )}
          {results.customers?.length > 0 && (
            <Card className="p-4">
              <h3 className="text-sm font-medium mb-3 flex items-center gap-2"><Users className="h-4 w-4" /> Customers ({results.customers.length})</h3>
              <div className="space-y-2">
                {results.customers.map((c: any) => (
                  <div key={c.id} className="text-sm py-1">{c.firstName} {c.lastName}</div>
                ))}
              </div>
            </Card>
          )}
          {results.suppliers?.length > 0 && (
            <Card className="p-4">
              <h3 className="text-sm font-medium mb-3 flex items-center gap-2"><Building2 className="h-4 w-4" /> Suppliers ({results.suppliers.length})</h3>
              <div className="space-y-2">
                {results.suppliers.map((s: any) => (
                  <div key={s.id} className="text-sm py-1">{s.companyName}</div>
                ))}
              </div>
            </Card>
          )}
          {(!results.trips?.length && !results.customers?.length && !results.suppliers?.length) && (
            <Card className="p-8 text-center text-sm text-muted-foreground">No results found</Card>
          )}
        </div>
      )}
    </div>
  );
}
