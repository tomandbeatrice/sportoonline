# PR Cleanup Execution Checklist

Use this checklist to track the PR cleanup execution.

## Pre-Execution Verification

- [ ] GitHub CLI installed and working (`gh --version`)
- [ ] Authenticated with GitHub (`gh auth login`)
- [ ] Repository permissions verified (can close PRs and edit labels)
- [ ] Read `docs/PR_CLEANUP_2025.md` documentation
- [ ] Read `scripts/PR_CLEANUP_README.md` script documentation

## Dry Run Testing

- [ ] Run dry run: `./scripts/cleanup-prs-master.sh --dry-run`
- [ ] Review dry run output
- [ ] Verify PR numbers and comments look correct
- [ ] No errors in dry run execution

## Execution

- [ ] Run cleanup: `./scripts/cleanup-prs-master.sh`
- [ ] Confirmed execution at prompt
- [ ] Step 1/3: 24 PRs closed successfully
- [ ] Step 2/3: Labels added successfully
- [ ] Step 3/3: Canonical markers added successfully
- [ ] No errors during execution

## Post-Execution Verification

### PR Count Verification
- [ ] Check open PRs: `gh pr list --state open`
- [ ] Verify 6 PRs are open (was 28)
- [ ] Check closed PRs: `gh pr list --state closed --limit 30`
- [ ] Verify 24 PRs recently closed

### Closed PRs Have Comments (24 PRs)
- [ ] PR #1 has closing comment referencing #11
- [ ] PR #2 has closing comment referencing #11
- [ ] PR #3 has closing comment referencing #11
- [ ] PR #4 has closing comment referencing #11
- [ ] PR #5 has closing comment referencing #11
- [ ] PR #6 has closing comment referencing #11
- [ ] PR #7 has closing comment referencing #11
- [ ] PR #8 has closing comment referencing #11
- [ ] PR #9 has closing comment referencing #11
- [ ] PR #10 has closing comment referencing #11
- [ ] PR #12 has closing comment referencing #11
- [ ] PR #13 has closing comment referencing #11
- [ ] PR #14 has closing comment referencing #11
- [ ] PR #41 has closing comment referencing #11
- [ ] PR #15 has closing comment referencing #19
- [ ] PR #16 has closing comment referencing #19
- [ ] PR #17 has closing comment referencing #19
- [ ] PR #18 has closing comment referencing #19
- [ ] PR #24 has closing comment referencing #28
- [ ] PR #25 has closing comment referencing #28
- [ ] PR #26 has closing comment referencing #28
- [ ] PR #27 has closing comment referencing #28
- [ ] PR #31 has closing comment referencing #32

### Canonical PRs Have Labels
- [ ] PR #11 has labels: `refactoring`, `high-priority`, `backend`, `frontend`
- [ ] PR #19 has labels: `upgrade`, `dependencies`, `laravel`
- [ ] PR #28 has labels: `feature`, `frontend`, `cross-sell`
- [ ] PR #32 has labels: `design-system`, `frontend`, `ui`

### WIP PRs Have Labels
- [ ] PR #33 has labels: `ci`, `work-in-progress`
- [ ] PR #37 has labels: `bug`, `work-in-progress`

### Canonical PRs Have Identification Comments
- [ ] PR #11 has canonical marker comment
- [ ] PR #19 has canonical marker comment
- [ ] PR #28 has canonical marker comment
- [ ] PR #32 has canonical marker comment

## Next Steps

- [ ] Notify team about cleanup completion
- [ ] Review PR #11 (File Structure Refactoring)
- [ ] Review PR #19 (Laravel 11 Upgrade)
- [ ] Review PR #28 (Transfer Modal)
- [ ] Review PR #32 (Design System)
- [ ] Merge canonical PRs in priority order
- [ ] Monitor WIP PRs (#33, #37) for completion
- [ ] Update CONTRIBUTING.md with PR submission guidelines

## Statistics

**Before Cleanup:**
- Total Open PRs: 28
- Duplicate/Redundant PRs: 24
- Canonical PRs: 4
- Work-in-Progress PRs: 2

**After Cleanup:**
- Total Open PRs: 6
- Active PRs Ready to Merge: 4
- Work-in-Progress PRs: 2
- Closed PRs: 24

**Reduction:** 78.6% reduction in open PRs (28 → 6)

## Issues/Notes

Record any issues or notes during execution:

```
[Add notes here]
```

---

**Executed by:** _______________  
**Date:** _______________  
**Result:** ☐ Success  ☐ Partial  ☐ Failed  
**Notes:** _______________________________________________
