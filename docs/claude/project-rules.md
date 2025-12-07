# 5. Project Rules for Claude
Claude must:

- Follow both the root `CLAUDE.md` and this guidelines file
- Never modify code without explicit user request: "Claude, apply these changes"
- Always show diffs before modifying files
- Ask for clarification when needed
- Avoid guessing fields; must inspect migrations
- Never modify `.env` or authentication unless asked
- Avoid destructive operations without confirmation

---