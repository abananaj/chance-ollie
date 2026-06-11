# agents.md

This file documents Claude Agents and automated routines that operate on this repository.

## Overview

Agents are autonomous AI-driven tasks that run on scheduled intervals or in response to events. This document tracks:
- What agents exist and what they do
- When they run (schedule)
- Who owns them
- What they can modify or access
- Any dependencies or prerequisites

## Scheduled Agents

### [Agent Name]

**Status**: [Active / Inactive / Proposed]  
**Owner**: [Person/team responsible]  
**Schedule**: [Cron pattern or interval, e.g., "Daily at 9 AM UTC"]  
**Last Run**: [Date/time]

#### Purpose
[Brief description of what this agent does]

#### Scope
- **Reads**: [What files/APIs it reads from]
- **Writes**: [What files/APIs it modifies]
- **Triggers**: [What causes it to run]

#### Configuration
- **Location**: [Where the agent definition lives, e.g., `.claude/agents/sync-styles.json`]
- **Permissions**: [Required permissions, e.g., "can edit src/json/*"]
- **Secrets/Env**: [Any environment variables or API keys needed]

#### Error Handling
[How failures are handled, who gets notified, what the recovery process is]

---

## Event-Driven Automations

### [Automation Name]

**Status**: [Active / Inactive / Proposed]  
**Trigger**: [GitHub event, webhook, manual invocation]

#### Purpose
[What this automation does when triggered]

#### Scope
- **Reads**: [What it accesses]
- **Writes**: [What it modifies]

#### Example Triggers
- On push to `main` branch
- On pull request labeled "theme-build"
- Manual `/run-sync` command

---

## Integration Points

### Slack / Teams / Discord

| Channel | Purpose | Agent |
|---------|---------|-------|
| [#channel-name] | [What notifications go here] | [Which agent posts] |

### GitHub Actions / CI-CD

[Document any CI workflows that interact with this theme]

### External APIs

[Document any API integrations: webhooks, data syncs, third-party services]

---

## Permissions & Access

### Agent Capabilities Matrix

| Agent | Read dist/ | Write dist/ | Read src/ | Write src/ | Edit theme.json | Git Commit | Git Push |
|-------|-----------|-----------|----------|-----------|-----------------|-----------|----------|
| [Agent] | ✓ | ✗ | ✓ | ✗ | ✗ | ✗ | ✗ |

---

## Common Automation Patterns

### Style Sync (JSON ↔ SCSS)

**When to use**: After updating theme.json colors/spacing, or SCSS variables  
**Command**: `npm run sync:j2s` or `npm run sync:s2j`  
**Agent**: [If automated]

### Theme Build Validation

**When to use**: Before merging PRs that touch `src/json/` or `src/index.scss`  
**Command**: `npm run build`  
**Agent**: [If automated]

### Pattern Registration Sync

**When to use**: After adding new patterns in `patterns/`  
**Command**: `npm run build` (rebuilds pattern list)  
**Agent**: [If automated]

---

## Troubleshooting

### Agent Fails to Run

1. Check the agent's last run log (if available)
2. Verify permissions are current (especially git credentials)
3. Check for blocked hooks or pre-commit failures
4. Review the agent's error output in logs

### Agent Modifies Files Unexpectedly

1. Review the agent's scope and permissions
2. Check if a hook in `.claude/settings.json` is triggering unintended behavior
3. Disable and re-enable the agent if configuration is out of sync

### Manual Intervention Needed

[Document how to pause/resume agents, force a run, or override results]

---

## Setup Checklist

- [ ] All agents documented here
- [ ] Permissions matrix is accurate
- [ ] Integration channels are correctly configured
- [ ] Error handling and notification contacts are set
- [ ] Secrets/env variables are stored securely
- [ ] Team members know how to disable/debug agents

---

## Future Agents / Ideas

### [Proposed Agent Name]

**Purpose**: [What we'd like to automate]  
**Status**: Not Yet Implemented  
**Blocker**: [What's needed before implementation]

---

**Last Updated**: [Date]  
**Maintained By**: [Person/team]
