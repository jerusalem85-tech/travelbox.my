<!-- BEGIN:nextjs-agent-rules -->
# This is NOT the Next.js you know

This version has breaking changes — APIs, conventions, and file structure may all differ from your training data. Read the relevant guide in `node_modules/next/dist/docs/` before writing any code. Heed deprecation notices.
<!-- END:nextjs-agent-rules -->

<!-- BEGIN:hostinger-mcp -->
# Hostinger MCP Server

This project has the Hostinger API MCP server configured in `opencode.json`.
Use these tools to manage hosting, domains, DNS, and deployments:

## Common tasks
- `hosting_deployStaticWebsite` - Deploy static site (out/ folder) to Hostinger
- `hosting_clearWebsiteCacheV1` - Clear server/CDN cache
- `hosting_enableCachelessModeV1` / `hosting_disableCachelessModeV1` - Toggle dev mode
- `hosting_listJsDeployments` / `hosting_showJsDeploymentLogs` - Check deployment status
- `DNS_*` tools - Manage DNS records
- `domains_*` tools - Manage domains

## Environment
- `HOSTINGER_API_TOKEN` env var must be set
- Domain: travelbox.my
- Hostinger username: u908372329

## Deployment
This project uses `output: "export"` (static site). The `out/` directory contents are deployed to `public_html/`.

### Manual deploy
Build and deploy from local:
```
npm run build
# Zip out/ and upload via API (requires HOSTINGER_API_TOKEN)
```

### Automated deploy (GitHub Actions)
Push to `master` triggers `.github/workflows/deploy.yml` which:
1. Builds the project
2. Zips `out/` contents
3. Uploads via Hostinger TUS file upload API
4. Triggers deploy to `public_html/`

The `HOSTINGER_API_TOKEN` secret must be set in GitHub repo.
<!-- END:hostinger-mcp -->
