# PR Cleanup Quick Start Guide

## TL;DR - Quick Execution

```bash
# 1. Preview what will happen (safe, no changes made)
./scripts/cleanup-prs-master.sh --dry-run

# 2. Execute the cleanup (will prompt for confirmation)
./scripts/cleanup-prs-master.sh
```

That's it! The scripts will automatically:
- Close 24 duplicate PRs with explanatory comments
- Add labels to 6 canonical/WIP PRs  
- Mark 4 canonical PRs with identification comments

## What Gets Changed

### PRs to be Closed (24)

**File Structure Refactoring** (14 PRs → keep #11)
- Close: #1, #2, #3, #4, #5, #6, #7, #8, #9, #10, #12, #13, #14, #41

**Laravel 11 Upgrade** (4 PRs → keep #19)
- Close: #15, #16, #17, #18

**Transfer Modal** (4 PRs → keep #28)
- Close: #24, #25, #26, #27

**Design System** (1 PR → keep #32)
- Close: #31

### PRs to Keep (6)

**Ready to Merge** (4 PRs)
- #11 - File Structure Refactoring ✅
- #19 - Laravel 11 Upgrade ✅
- #28 - Transfer Modal ✅
- #32 - Design System ✅

**Work in Progress** (2 PRs)
- #33 - CI Stabilization 🔄
- #37 - TODO Fixes 🔄

## Prerequisites

Ensure you have:
1. GitHub CLI installed: `gh --version`
2. Authenticated: `gh auth login`
3. Write permissions on the repository

## Safety

- **Dry run mode** lets you preview changes safely
- **Confirmation prompt** before making changes
- **Detailed output** shows exactly what's being done
- All scripts have error handling

## After Cleanup

1. **Verify** results: `gh pr list --state open` (should show 6 PRs)
2. **Review** canonical PRs
3. **Merge** in priority order: #11 → #19 → #28 → #32
4. **Monitor** WIP PRs: #33, #37

## Help

- Full docs: `docs/PR_CLEANUP_2025.md`
- Script docs: `scripts/PR_CLEANUP_README.md`
- Questions? Open a discussion

---

**Expected Result:** 28 open PRs → 6 open PRs (78.6% reduction)
