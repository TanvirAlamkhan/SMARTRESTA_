@echo off
echo ========================================================================
echo SMARTRESTA — GitHub Upload Script
echo Repository: https://github.com/TanvirAlamkhan/SMARTRESTA.git
echo ========================================================================

cd /d "%~dp0"

echo Staging all changes...
git add .

echo Enter commit message (or press ENTER for default):
set /p commit_msg="Commit Message: "
if "%commit_msg%"=="" set commit_msg="SMARTRESTA Production Deployment Update"

echo Committing changes...
git commit -m "%commit_msg%"

echo Pushing to GitHub (origin/master)...
git push origin master

echo ========================================================================
echo SUCCESS! Code successfully pushed to GitHub repository!
echo ========================================================================
pause
