#!/bin/bash
# Mark Canonical PRs
# This script adds pinned comments to canonical PRs identifying them as such
# 
# Prerequisites:
# 1. GitHub CLI (gh) installed and authenticated
# 2. Appropriate permissions to comment on PRs in the repository
#
# Usage: ./scripts/mark-canonical-prs.sh [--dry-run]
#
# Options:
#   --dry-run    Show what would be done without actually adding comments

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
    echo -e "${YELLOW}Running in DRY RUN mode - no comments will be added${NC}\n"
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

# Function to add a canonical comment to a PR
add_canonical_comment() {
    local pr_number=$1
    local comment=$2
    
    echo -e "${BLUE}Adding canonical marker to PR #${pr_number}...${NC}"
    
    if [[ "$DRY_RUN" == true ]]; then
        echo -e "${YELLOW}[DRY RUN] Would add comment to PR #${pr_number}:${NC}"
        echo "$comment"
        echo ""
    else
        if gh pr comment "$pr_number" --body "$comment"; then
            echo -e "${GREEN}✓ Added canonical marker to PR #${pr_number}${NC}"
        else
            echo -e "${RED}✗ Failed to add comment to PR #${pr_number}${NC}"
            return 1
        fi
    fi
    
    echo ""
}

echo -e "${BLUE}=== Marking Canonical PRs ===${NC}\n"

# PR #11 - File Structure Refactoring
PR11_COMMENT="📌 **This is the canonical PR for file structure refactoring.**

This PR consolidates all file structure reorganization work from multiple related PRs (#1, #2, #3, #4, #5, #6, #7, #8, #9, #10, #12, #13, #14, #41).

**What this PR does:**
- Migrates 400+ files to standard Laravel/Vue directory structure
- Organizes controllers into \`app/Http/Controllers/\`
- Organizes models into \`app/Models/\`
- Organizes Vue components into standard structure
- Ensures all builds and tests pass

**Status:** ✅ Ready for review and merge

**Related Documentation:** See \`docs/PR_CLEANUP_2025.md\` for cleanup details."

add_canonical_comment 11 "$PR11_COMMENT"

# PR #19 - Laravel 11 Upgrade
PR19_COMMENT="📌 **This is the canonical PR for Laravel 11 upgrade.**

This PR consolidates all Laravel 11 upgrade work from multiple related PRs (#15, #16, #17, #18).

**What this PR does:**
- Upgrades Laravel from 10.x to 11.x
- Updates all required dependencies
- Updates configuration files for Laravel 11 compatibility
- Ensures backward compatibility

**Status:** ✅ Ready for review and merge

**Related Documentation:** 
- \`docs/LARAVEL_11_UPGRADE.md\` for upgrade details
- \`docs/PR_CLEANUP_2025.md\` for cleanup details"

add_canonical_comment 19 "$PR19_COMMENT"

# PR #28 - Transfer Modal
PR28_COMMENT="📌 **This is the canonical PR for Hotel→Transfer cross-sell feature.**

This PR consolidates all transfer recommendation modal work from multiple related PRs (#24, #25, #26, #27).

**What this PR does:**
- Implements cross-promotion modal for Hotel→Transfer upsell
- Includes comprehensive test coverage (unit + integration)
- Implements full accessibility support (ARIA, keyboard navigation)
- Production-ready implementation with proper error handling

**Status:** ✅ Ready for review and merge

**Related Documentation:**
- \`docs/CROSS-SELL-FEATURE.md\` for feature details
- \`docs/PR_CLEANUP_2025.md\` for cleanup details"

add_canonical_comment 28 "$PR28_COMMENT"

# PR #32 - Design System & Color Palette
PR32_COMMENT="📌 **This is the canonical PR for SuperApp Design System.**

This PR supersedes PR #31 with better documentation and organization.

**What this PR does:**
- Implements unified SuperApp color palette
- Provides comprehensive documentation
- Includes extensive usage examples
- Implements brand and vertical theming
- Ensures consistency across all platforms

**Status:** ✅ Ready for review and merge

**Related Documentation:** See \`docs/PR_CLEANUP_2025.md\` for cleanup details."

add_canonical_comment 32 "$PR32_COMMENT"

# Summary
echo -e "${GREEN}=== Summary ===${NC}"
if [[ "$DRY_RUN" == true ]]; then
    echo -e "${YELLOW}DRY RUN completed. No comments were actually added.${NC}"
else
    echo -e "${GREEN}Successfully marked all canonical PRs.${NC}"
fi

echo ""
echo "Canonical markers added to:"
echo "  - PR #11: File Structure Refactoring"
echo "  - PR #19: Laravel 11 Upgrade"
echo "  - PR #28: Transfer Modal"
echo "  - PR #32: Design System & Color Palette"
echo ""
echo -e "${GREEN}✓ Canonical marking complete!${NC}"
