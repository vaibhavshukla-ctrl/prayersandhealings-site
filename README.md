# prayersandhealings-site

Source files for prayersandhealings.com, deployed to BigRock hosting via cPanel Git Version Control.

## Structure
- `public_html/` — everything that goes live on the site
- `.cpanel.yml` — tells cPanel where to copy files on deploy

## IMPORTANT: not included in this repo
- `db-config.php` — contains your real database password. Create this manually via cPanel File Manager,
  never commit it to GitHub. See the db-config.php template shared separately.
- `images/` folder — your photos already live directly on BigRock. Add them to this repo later if you
  want them version controlled too, for now they are managed directly through cPanel File Manager.

## Deploying updates
1. Push your changes to the `main` branch on GitHub.
2. In cPanel → Git Version Control → open this repository → "Pull or Deploy" tab.
3. Click "Update from Remote", then "Deploy HEAD Commit".
