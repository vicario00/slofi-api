# Skill Registry

**Delegator use only.** Any agent that launches sub-agents reads this registry to resolve compact rules, then injects them directly into sub-agent prompts. Sub-agents do NOT read this registry or individual SKILL.md files.

See `_shared/skill-resolver.md` for the full resolution protocol.

## User Skills

| Trigger | Skill | Path |
|---------|-------|------|
| When creating a pull request, opening a PR, or preparing changes for review | branch-pr | /home/erickesc/.config/opencode/skills/branch-pr/SKILL.md |
| When writing Go tests, using teatest, or adding test coverage | go-testing | /home/erickesc/.config/opencode/skills/go-testing/SKILL.md |
| When creating a GitHub issue, reporting a bug, or requesting a feature | issue-creation | /home/erickesc/.config/opencode/skills/issue-creation/SKILL.md |
| When user says "judgment day", "judgment-day", "review adversarial", "dual review", "doble review", "juzgar", "que lo juzguen" | judgment-day | /home/erickesc/.config/opencode/skills/judgment-day/SKILL.md |
| When editing ~/.config/hypr/, waybar, walker, alacritty, kitty, ghostty, mako, or omarchy configs | omarchy | /home/erickesc/.claude/skills/omarchy/SKILL.md |

## Compact Rules

Pre-digested rules per skill. Delegators copy matching blocks into sub-agent prompts as `## Project Standards (auto-resolved)`.

### branch-pr
- Every PR MUST link an approved issue (`status:approved`) — no exceptions
- Every PR MUST have exactly one `type:*` label
- Automated checks must pass before merge
- Branch naming: `type/description` — lowercase, regex `^(feat|fix|chore|docs|style|refactor|perf|test|build|ci|revert)\/[a-z0-9._-]+$`
- PR body MUST include `Closes #<issue-number>` in the description
- Blank PRs without issue linkage will be blocked by GitHub Actions

### go-testing
- Use table-driven tests: `tests := []struct{ name, input, expected string; wantErr bool }{ ... }`
- Run subtests with `t.Run(tt.name, func(t *testing.T) { ... })`
- For Bubbletea TUI: test Model state transitions via `m.Update(tea.KeyMsg{...})` — no need for full render
- Use golden files for complex output: `testdata/golden/*.txt`, update with `-update` flag
- Integration tests: use `net/http/httptest` (built-in, no extra deps)
- Coverage: `go test -cover ./...` or `go test -coverprofile=coverage.out ./...`
- Never skip error checks in tests — always `if err != nil { t.Fatal(err) }`

### issue-creation
- Blank issues are disabled — MUST use a template (bug_report.yml or feature_request.yml)
- Every issue gets `status:needs-review` automatically on creation
- A maintainer MUST add `status:approved` before any PR can be opened
- Search for duplicates BEFORE creating a new issue
- Questions go to Discussions, NOT issues
- Fill ALL required template fields — never submit partial issues

### judgment-day
- Launch TWO sub-agents via `delegate` in parallel (never sequential)
- Each judge receives the SAME target but works independently — no cross-contamination
- Orchestrator (NOT a sub-agent) synthesizes results: Confirmed = both found it; Suspect A/B = only one found it; Contradiction = agents disagree
- Resolve skill registry BEFORE launching judges and inject compact rules into BOTH judge prompts
- Max 2 re-judge iterations, then escalate to human if unresolved
- Classify every WARNING as `actionable` (fix before merge) or `informational` (tracked but non-blocking)

### omarchy
- NEVER edit files in `~/.local/share/omarchy/` — read-only; changes lost on `omarchy-update`
- User customizations go in `~/.config/hypr/`, `~/.config/waybar/`, `~/.config/omarchy/` etc.
- For keybindings: edit `~/.config/hypr/keybindings.conf`, NOT the source templates
- For themes: use `omarchy-theme-set <theme>` command, don't edit theme files directly
- Read `~/.local/share/omarchy/` freely to understand commands — it's safe to inspect
- Always check `~/.config/omarchy/` for user overrides before reading Omarchy source defaults

## Project Conventions

| File | Path | Notes |
|------|------|-------|

No project-level convention files found in project root (AGENTS.md, CLAUDE.md, .cursorrules, GEMINI.md, copilot-instructions.md).

---
_Generated: 2026-04-09 — nameless-finance-app (Slofi)_
