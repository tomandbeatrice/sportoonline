# PR Cleanup Scripts

This directory contains automation scripts for the December 2025 PR cleanup initiative.

## Overview

The sportoonline repository had 28 open PRs with significant duplication. These scripts automate the process of closing 24 duplicate PRs and organizing the 6 remaining canonical/WIP PRs.

For detailed documentation about the cleanup rationale, see `docs/PR_CLEANUP_2025.md`.

## Prerequisites

1. **GitHub CLI** (`gh`) must be installed
   - Install from: https://cli.github.com/
   - Verify with: `gh --version`

2. **Authentication** required
   - Run: `gh auth login`
   - Ensure you have permissions to:
     - Close PRs
     - Comment on PRs
     - Edit PR labels

3. **Repository access**
   - Must be run from the repository root directory
   - Must have appropriate GitHub permissions for the repository

## Quick Start

### Option 1: Use Master Script (Recommended)

Run all cleanup steps in sequence:

```bash
# Dry run (preview changes without executing)
./scripts/cleanup-prs-master.sh --dry-run

# Execute cleanup
./scripts/cleanup-prs-master.sh
```

The master script will:
1. Close 24 duplicate PRs with explanatory comments
2. Add appropriate labels to 6 canonical/WIP PRs
3. Add identification comments to 4 canonical PRs

### Option 2: Run Individual Scripts

Execute cleanup steps separately:

```bash
# Step 1: Close duplicate PRs
./scripts/close-duplicate-prs.sh [--dry-run]

# Step 2: Add labels to canonical PRs
./scripts/label-canonical-prs.sh [--dry-run]

# Step 3: Mark canonical PRs
./scripts/mark-canonical-prs.sh [--dry-run]
```

## Script Descriptions

### cleanup-prs-master.sh

**Purpose:** Orchestrates the complete PR cleanup process

**What it does:**
- Runs all three cleanup scripts in sequence
- Provides progress updates and error handling
- Shows summary of changes made

**Usage:**
```bash
./scripts/cleanup-prs-master.sh [--dry-run]
```

**Options:**
- `--dry-run`: Preview changes without executing

### close-duplicate-prs.sh

**Purpose:** Close 24 duplicate PRs with explanatory comments

**What it does:**
- Closes PRs #1-10, #12-18, #24-27, #31, #41
- Adds appropriate closing comment to each PR
- References the superseding canonical PR in each comment

**PRs affected:**
- 14 file structure refactoring PRs → superseded by #11
- 4 Laravel 11 upgrade PRs → superseded by #19
- 4 transfer modal PRs → superseded by #28
- 1 design system PR → superseded by #32

**Usage:**
```bash
./scripts/close-duplicate-prs.sh [--dry-run]
```

### label-canonical-prs.sh

**Purpose:** Add appropriate labels to canonical and WIP PRs

**What it does:**
- Adds labels to PRs #11, #19, #28, #32, #33, #37
- Uses consistent label taxonomy

**Labels added:**
- PR #11: `refactoring`, `high-priority`, `backend`, `frontend`
- PR #19: `upgrade`, `dependencies`, `laravel`
- PR #28: `feature`, `frontend`, `cross-sell`
- PR #32: `design-system`, `frontend`, `ui`
- PR #33: `ci`, `work-in-progress`
- PR #37: `bug`, `work-in-progress`

**Usage:**
```bash
./scripts/label-canonical-prs.sh [--dry-run]
```

### mark-canonical-prs.sh

**Purpose:** Add identification comments to canonical PRs

**What it does:**
- Adds pinned comments to PRs #11, #19, #28, #32
- Marks them as canonical PRs for their respective features
- Includes links to related documentation

**Usage:**
```bash
./scripts/mark-canonical-prs.sh [--dry-run]
```

## Dry Run Mode

All scripts support `--dry-run` mode to preview changes:

```bash
./scripts/cleanup-prs-master.sh --dry-run
```

**Dry run mode:**
- Shows what would be done
- Does NOT make any actual changes
- Safe to run multiple times
- Useful for verification before execution

## Safety Features

1. **Confirmation prompt** in master script (live mode only)
2. **Error handling** - scripts stop on first error
3. **Dry run mode** - preview before executing
4. **Detailed output** - shows exactly what's being done
5. **Authentication check** - verifies GitHub CLI access

## Expected Results

**Before cleanup:**
- Total open PRs: 28
- Duplicate/redundant PRs: 24
- Canonical PRs: 4
- Work-in-progress PRs: 2

**After cleanup:**
- Total open PRs: 6
- Active PRs ready to merge: 4 (#11, #19, #28, #32)
- Work-in-progress PRs: 2 (#33, #37)
- Closed PRs: 24

**Reduction:** 78.6% reduction in open PRs

## Verification

After running the cleanup, verify:

1. **PR count:**
   ```bash
   gh pr list --state open
   ```
   Should show 6 open PRs

2. **Closed PRs:**
   ```bash
   gh pr list --state closed --limit 30
   ```
   Should show 24 recently closed PRs

3. **Labels:**
   ```bash
   gh pr view 11
   gh pr view 19
   gh pr view 28
   gh pr view 32
   ```
   Should show appropriate labels on each

4. **Comments:**
   - Check that closed PRs have explanatory comments
   - Check that canonical PRs have identification comments

## Troubleshooting

### "gh: command not found"
- Install GitHub CLI from https://cli.github.com/

### "Not authenticated with GitHub CLI"
- Run: `gh auth login`

### "Failed to close PR"
- Check repository permissions
- Verify you have write access to the repository
- Check if PR is already closed

### "Failed to add label"
- Labels might not exist in the repository
- Create missing labels in repository settings
- Or modify script to use existing labels

## Next Steps After Cleanup

1. **Review canonical PRs** (#11, #19, #28, #32)
2. **Merge canonical PRs** in priority order:
   - Priority 1: #11 (file structure - foundation for other work)
   - Priority 2: #19 (Laravel 11 upgrade)
   - Priority 3: #28 (transfer modal feature)
   - Priority 4: #32 (design system)
3. **Monitor WIP PRs** (#33, #37) for completion
4. **Communicate cleanup** to team
5. **Update CONTRIBUTING.md** with PR submission guidelines

## Related Documentation

- `docs/PR_CLEANUP_2025.md` - Complete cleanup documentation
- `docs/LARAVEL_11_UPGRADE.md` - Laravel 11 upgrade details
- `docs/CROSS-SELL-FEATURE.md` - Transfer modal feature details

## Support

For questions or issues with these scripts:
1. Review `docs/PR_CLEANUP_2025.md` for detailed context
2. Check this README for usage instructions
3. Open a discussion in the repository

---

**Last Updated:** December 13, 2025  
**Version:** 1.0  
**Maintained by:** sportoonline team
