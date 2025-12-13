#!/bin/bash
# Add Labels to Canonical PRs
# This script adds appropriate labels to the canonical PRs that are being kept
# 
# Prerequisites:
# 1. GitHub CLI (gh) installed and authenticated
# 2. Appropriate permissions to edit labels in the repository
#
# Usage: ./scripts/label-canonical-prs.sh [--dry-run]
#
# Options:
#   --dry-run    Show what would be done without actually adding labels

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
    echo -e "${YELLOW}Running in DRY RUN mode - no labels will be added${NC}\n"
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

# Function to add labels to a PR
add_labels_to_pr() {
    local pr_number=$1
    shift
    local labels=("$@")
    
    echo -e "${BLUE}Adding labels to PR #${pr_number}...${NC}"
    
    if [[ "$DRY_RUN" == true ]]; then
        echo -e "${YELLOW}[DRY RUN] Would add labels to PR #${pr_number}: ${labels[*]}${NC}"
    else
        for label in "${labels[@]}"; do
            if gh pr edit "$pr_number" --add-label "$label"; then
                echo -e "${GREEN}✓ Added label '${label}' to PR #${pr_number}${NC}"
            else
                echo -e "${RED}✗ Failed to add label '${label}' to PR #${pr_number}${NC}"
            fi
        done
    fi
    
    echo ""
}

echo -e "${BLUE}=== Adding Labels to Canonical PRs ===${NC}\n"

# PR #11 - File Structure Refactoring
add_labels_to_pr 11 "refactoring" "high-priority" "backend" "frontend"

# PR #19 - Laravel 11 Upgrade
add_labels_to_pr 19 "upgrade" "dependencies" "laravel"

# PR #28 - Transfer Modal
add_labels_to_pr 28 "feature" "frontend" "cross-sell"

# PR #32 - Design System & Color Palette
add_labels_to_pr 32 "design-system" "frontend" "ui"

# PR #33 - CI Stabilization (WIP)
add_labels_to_pr 33 "ci" "work-in-progress"

# PR #37 - TODO Fixes (WIP)
add_labels_to_pr 37 "bug" "work-in-progress"

# Summary
echo -e "${GREEN}=== Summary ===${NC}"
if [[ "$DRY_RUN" == true ]]; then
    echo -e "${YELLOW}DRY RUN completed. No labels were actually added.${NC}"
else
    echo -e "${GREEN}Successfully added labels to canonical PRs.${NC}"
fi

echo ""
echo "Labels added:"
echo "  - PR #11: refactoring, high-priority, backend, frontend"
echo "  - PR #19: upgrade, dependencies, laravel"
echo "  - PR #28: feature, frontend, cross-sell"
echo "  - PR #32: design-system, frontend, ui"
echo "  - PR #33: ci, work-in-progress"
echo "  - PR #37: bug, work-in-progress"
echo ""
echo -e "${GREEN}✓ Label addition complete!${NC}"
