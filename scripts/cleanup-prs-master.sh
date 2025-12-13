#!/bin/bash
# Master PR Cleanup Script
# This script orchestrates the complete PR cleanup process
# 
# Prerequisites:
# 1. GitHub CLI (gh) installed and authenticated
# 2. Appropriate permissions in the repository
#
# Usage: ./scripts/cleanup-prs-master.sh [--dry-run]
#
# Options:
#   --dry-run    Show what would be done without actually making changes

set -euo pipefail

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
BOLD='\033[1m'
NC='\033[0m' # No Color

# Script directory
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"

# Check if running in dry-run mode
DRY_RUN_FLAG=""
if [[ "${1:-}" == "--dry-run" ]]; then
    DRY_RUN_FLAG="--dry-run"
    echo -e "${BOLD}${YELLOW}╔════════════════════════════════════════════════════════════╗${NC}"
    echo -e "${BOLD}${YELLOW}║          PR CLEANUP - DRY RUN MODE                         ║${NC}"
    echo -e "${BOLD}${YELLOW}║          No changes will be made                           ║${NC}"
    echo -e "${BOLD}${YELLOW}╚════════════════════════════════════════════════════════════╝${NC}"
    echo ""
else
    echo -e "${BOLD}${BLUE}╔════════════════════════════════════════════════════════════╗${NC}"
    echo -e "${BOLD}${BLUE}║          PR CLEANUP - LIVE MODE                            ║${NC}"
    echo -e "${BOLD}${BLUE}║          Changes will be made to PRs                       ║${NC}"
    echo -e "${BOLD}${BLUE}╚════════════════════════════════════════════════════════════╝${NC}"
    echo ""
    
    # Confirm before proceeding
    read -p "This will close 24 PRs and modify 6 others. Continue? (yes/no): " -r
    echo
    if [[ ! $REPLY =~ ^[Yy]es$ ]]; then
        echo -e "${YELLOW}Aborted by user.${NC}"
        exit 0
    fi
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

echo -e "${BOLD}Starting PR Cleanup Process...${NC}\n"

# Step 1: Close duplicate PRs
echo -e "${BOLD}${BLUE}═══════════════════════════════════════════════════════════${NC}"
echo -e "${BOLD}${BLUE}Step 1/3: Closing 24 Duplicate PRs${NC}"
echo -e "${BOLD}${BLUE}═══════════════════════════════════════════════════════════${NC}\n"

if bash "$SCRIPT_DIR/close-duplicate-prs.sh" $DRY_RUN_FLAG; then
    echo -e "${GREEN}✓ Step 1 completed successfully${NC}\n"
else
    echo -e "${RED}✗ Step 1 failed${NC}"
    exit 1
fi

# Step 2: Add labels to canonical PRs
echo -e "${BOLD}${BLUE}═══════════════════════════════════════════════════════════${NC}"
echo -e "${BOLD}${BLUE}Step 2/3: Adding Labels to Canonical PRs${NC}"
echo -e "${BOLD}${BLUE}═══════════════════════════════════════════════════════════${NC}\n"

if bash "$SCRIPT_DIR/label-canonical-prs.sh" $DRY_RUN_FLAG; then
    echo -e "${GREEN}✓ Step 2 completed successfully${NC}\n"
else
    echo -e "${RED}✗ Step 2 failed${NC}"
    exit 1
fi

# Step 3: Mark canonical PRs
echo -e "${BOLD}${BLUE}═══════════════════════════════════════════════════════════${NC}"
echo -e "${BOLD}${BLUE}Step 3/3: Marking Canonical PRs${NC}"
echo -e "${BOLD}${BLUE}═══════════════════════════════════════════════════════════${NC}\n"

if bash "$SCRIPT_DIR/mark-canonical-prs.sh" $DRY_RUN_FLAG; then
    echo -e "${GREEN}✓ Step 3 completed successfully${NC}\n"
else
    echo -e "${RED}✗ Step 3 failed${NC}"
    exit 1
fi

# Final Summary
echo -e "${BOLD}${GREEN}╔════════════════════════════════════════════════════════════╗${NC}"
echo -e "${BOLD}${GREEN}║          PR CLEANUP COMPLETED SUCCESSFULLY!                ║${NC}"
echo -e "${BOLD}${GREEN}╚════════════════════════════════════════════════════════════╝${NC}\n"

if [[ -n "$DRY_RUN_FLAG" ]]; then
    echo -e "${YELLOW}This was a DRY RUN. No actual changes were made.${NC}"
    echo -e "${YELLOW}Run without --dry-run to execute the cleanup.${NC}\n"
else
    echo -e "${GREEN}PR cleanup has been executed successfully!${NC}\n"
    
    echo "What was done:"
    echo "  ✓ Closed 24 duplicate PRs with explanatory comments"
    echo "  ✓ Added labels to 6 canonical/WIP PRs"
    echo "  ✓ Marked 4 canonical PRs with identification comments"
    echo ""
    echo "Current PR status:"
    echo "  • Total open PRs: 6 (was 28)"
    echo "  • Ready to merge: 4 canonical PRs"
    echo "  • Work in progress: 2 WIP PRs"
    echo "  • Closed: 24 duplicate PRs"
    echo ""
    echo "Next steps:"
    echo "  1. Review canonical PRs (#11, #19, #28, #32)"
    echo "  2. Merge canonical PRs in priority order"
    echo "  3. Monitor WIP PRs (#33, #37) for completion"
    echo "  4. Communicate cleanup to team"
    echo ""
    echo "Documentation:"
    echo "  • See docs/PR_CLEANUP_2025.md for full details"
    echo ""
fi

echo -e "${GREEN}✓ All done!${NC}"
