Git Workflow
=============

Stay up to date
---------------
At any time, you can run the `update.sh` script to update all vendors and rebuild the project.
If you only want to clear the back-end cache/database, run the `back/clear.sh` script.

Branches description
--------------------
  * The **master** branch corresponds to the **production** environment where the final project is
  * The **version** branch corresponds to the stable **pre-production** environment. Each version branch should match the following pattern: **vX.X.X**
  * Other branches are **development** branches. Each development branch should match a **feature**, and match the following pattern: **name-of-the-feature**


How to use Git on the project
-----------------------------
  * Create a new branch **name-of-the-feature** from the latest **version** branch
  * Commit your modifications on it. Eventually, don't forget to include database migrations generated with `app/console doctrine:migrations:diff`
  * Merge your custom branch on **version** via a Pull Request
  * Deploy on the **pre-production** environment
  * Run tests and check everything is good
  * Upon validation, merge **version** onto **master** via a Pull Request
  * Deploy on the **prod** environment

**NOTE**: Do not merge **features** on **master** otherwise you will merge not validated features and deploy them on the **prod** environment!
