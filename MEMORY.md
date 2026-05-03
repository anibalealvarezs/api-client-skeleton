# API Client Skeleton Memory
## Scope
- Package role: Abstraction (Core)
- Purpose: This package operates within the Abstraction (Core) layer of the APIs Hub SaaS hierarchy, providing the core client functionality for all SDKs.
- Dependency stance: Consumes `anibalealvarezs/oauth-v1` and serves all other SDKs.
## Local working rules
- Consult `AGENTS.md` first for package-specific instructions.
- Use this `MEMORY.md` for repository-specific decisions, learnings, and follow-up notes.
- Use `D:\laragon\www\_shared\AGENTS.md` and `D:\laragon\www\_shared\MEMORY.md` for cross-repository protocols and workspace-wide learnings.
- Keep secrets, credentials, tokens, and private endpoints out of this file.
## Current notes
- Shared SDK foundation for authentication, transport, retries, and error handling.