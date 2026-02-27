#!/usr/bin/env bash
# Clone NGM-Web (newgo) to get the CODE ONLY, then create a NEW git repo (NGNMOTOR).
# Nothing is ever pushed to or updated on the original repo.

set -e
WORKSPACE="/Users/abdulbasit/NGNWEBTONGN"
REPO="git@github.com:NeguinhoAdmin/NGM-Web.git"
BRANCH="newgo"
TARGET="NGNMOTOR"

cd "$WORKSPACE"

# Remove existing folder so we get a clean clone
if [ -d "$TARGET" ]; then
  echo "Removing existing $TARGET so we can clone fresh..."
  rm -rf "$TARGET"
fi

# Clone (we only need the code)
echo "Cloning $REPO (branch: $BRANCH) into $TARGET..."
git clone --branch "$BRANCH" "$REPO" "$TARGET"

cd "$TARGET"

# Disconnect from original repo: delete .git and start a new repo
echo "Disconnecting from original repo and creating new git repo..."
rm -rf .git
git init
git add .
git commit -m "NGNMOTOR base (code from NGM-Web newgo)"

echo "Done. $TARGET now has the code and a NEW git repo (no remote to NGM-Web)."
echo "Next: run the upgrade script from inside $TARGET:"
echo "  cd $WORKSPACE/$TARGET && bash ../upgrade-to-l12-livewire4-flux-backpack.sh"
echo ""
echo "To add your own remote later: git remote add origin <your-new-repo-url>"
