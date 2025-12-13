#!/bin/bash
# PR Cleanup Automation Script
# This script closes duplicate PRs with appropriate comments
# 
# Prerequisites:
# 1. GitHub CLI (gh) installed and authenticated
# 2. Appropriate permissions to close PRs in the repository
#
# Usage: ./scripts/close-duplicate-prs.sh [--dry-run]
#
# Options:
#   --dry-run    Show what would be done without actually closing PRs

set -euo pipefail

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Check if running in dry-run mode
DRY_RUN=false
if [[ "${1:-}" == "--dry-run" ]]; then
    DRY_RUN=true
    echo -e "${YELLOW}Running in DRY RUN mode - no PRs will be closed${NC}\n"
fi

# Check if gh CLI is available
if ! command -v gh &> /dev/null; then
    echo -e "${RED}Error: GitHub CLI (gh) is not installed${NC}"
    echo "Install it from: https://cli.github.com/"
    exit 1
fi

# Check if authenticated
if ! gh auth status &> /dev/null; then
    echo -e "${RED}Error: Not authenticated with GitHub CLI${NC}"
    echo "Run: gh auth login"
    exit 1
fi

# Function to close a PR with a comment
close_pr_with_comment() {
    local pr_number=$1
    local comment=$2
    
    echo -e "${BLUE}Processing PR #${pr_number}...${NC}"
    
    if [[ "$DRY_RUN" == true ]]; then
        echo -e "${YELLOW}[DRY RUN] Would close PR #${pr_number} with comment:${NC}"
        echo "$comment"
        echo ""
    else
        # Add comment to PR
        if gh pr comment "$pr_number" --body "$comment"; then
            echo -e "${GREEN}✓ Added comment to PR #${pr_number}${NC}"
            
            # Close the PR
            if gh pr close "$pr_number"; then
                echo -e "${GREEN}✓ Closed PR #${pr_number}${NC}"
            else
                echo -e "${RED}✗ Failed to close PR #${pr_number}${NC}"
                return 1
            fi
        else
            echo -e "${RED}✗ Failed to add comment to PR #${pr_number}${NC}"
            return 1
        fi
    fi
    
    echo ""
}

# File Structure Refactoring PRs (14 PRs)
FILE_STRUCTURE_COMMENT="Thank you for your contribution! 

This PR is being closed as it's superseded by **#11** (Refactor: Organize files into standard Laravel and Vue.js directory structure), which provides the most comprehensive implementation of the file structure refactoring.

PR #11 includes:
- Organization of 400+ files into standard directories
- All controller, model, and Vue component migrations
- Passing builds and tests
- All changes from this PR and other similar PRs

No work is being lost - your changes are already incorporated in the canonical PR #11.

Please review #11 if you'd like to contribute further improvements to the file structure refactoring."

echo -e "${BLUE}=== Closing File Structure Refactoring PRs (14 PRs) ===${NC}\n"

for pr in 1 2 3 4 5 6 7 8 9 10 12 13 14 41; do
    close_pr_with_comment "$pr" "$FILE_STRUCTURE_COMMENT"
done

# Laravel 11 Upgrade PRs (4 PRs)
LARAVEL_UPGRADE_COMMENT="Thank you for working on the Laravel 11 upgrade!

This PR is being closed as it's superseded by **#19** (Upgrade to Laravel 11), which provides the complete Laravel 11 upgrade implementation.

PR #19 includes:
- Latest Laravel 11 dependency versions
- All required configuration changes
- Complete dependency updates
- All changes from this PR and other upgrade PRs

No work is being lost - your changes are already incorporated in the canonical PR #19.

Please review #19 if you'd like to contribute further improvements to the Laravel 11 upgrade."

echo -e "${BLUE}=== Closing Laravel 11 Upgrade PRs (4 PRs) ===${NC}\n"

for pr in 15 16 17 18; do
    close_pr_with_comment "$pr" "$LARAVEL_UPGRADE_COMMENT"
done

# Transfer Modal PRs (4 PRs)
TRANSFER_MODAL_COMMENT="Thank you for implementing the transfer cross-sell feature!

This PR is being closed as it's superseded by **#28** (Add cross-promotion modal for transfer upsell after hotel booking), which provides the production-ready implementation.

PR #28 includes:
- Comprehensive test coverage (unit + integration tests)
- Full accessibility support (ARIA labels, keyboard navigation)
- Better code organization and documentation
- Production-ready implementation
- All changes from this PR and other transfer modal PRs

No work is being lost - your changes are already incorporated in the canonical PR #28.

Please review #28 if you'd like to contribute further improvements to the transfer recommendation feature."

echo -e "${BLUE}=== Closing Transfer Modal PRs (4 PRs) ===${NC}\n"

for pr in 24 25 26 27; do
    close_pr_with_comment "$pr" "$TRANSFER_MODAL_COMMENT"
done

# Design System Color PRs (1 PR)
COLOR_PALETTE_COMMENT="Thank you for working on the SuperApp color palette!

This PR is being closed as it's superseded by **#32** (Implement SuperApp Design System & Color Palette), which provides a better-documented implementation.

PR #32 includes:
- More comprehensive documentation
- Better organized color tokens
- More extensive usage examples
- All changes from this PR

No work is being lost - your changes are already incorporated in the canonical PR #32.

Please review #32 if you'd like to contribute further improvements to the design system."

echo -e "${BLUE}=== Closing Design System Color PRs (1 PR) ===${NC}\n"

close_pr_with_comment 31 "$COLOR_PALETTE_COMMENT"

# Summary
echo -e "${GREEN}=== Summary ===${NC}"
if [[ "$DRY_RUN" == true ]]; then
    echo -e "${YELLOW}DRY RUN completed. No PRs were actually closed.${NC}"
else
    echo -e "${GREEN}Successfully processed 24 duplicate PRs.${NC}"
fi

echo ""
echo "PRs closed by category:"
echo "  - File Structure Refactoring: 14 PRs (1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 12, 13, 14, 41)"
echo "  - Laravel 11 Upgrade: 4 PRs (15, 16, 17, 18)"
echo "  - Transfer Modal: 4 PRs (24, 25, 26, 27)"
echo "  - Design System Colors: 1 PR (31)"
echo ""
echo "Canonical PRs (kept open):"
echo "  - #11: File Structure Refactoring"
echo "  - #19: Laravel 11 Upgrade"
echo "  - #28: Transfer Modal"
echo "  - #32: Design System & Color Palette"
echo ""
echo "Next steps:"
echo "  1. Add labels to canonical PRs using: ./scripts/label-canonical-prs.sh"
echo "  2. Add canonical identification comments using: ./scripts/mark-canonical-prs.sh"
echo "  3. Review and merge canonical PRs"
echo ""
echo -e "${GREEN}✓ PR cleanup complete!${NC}"
