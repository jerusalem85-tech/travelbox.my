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
<!-- END:hostinger-mcp -->
