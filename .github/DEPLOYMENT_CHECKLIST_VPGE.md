### 🔁 Rollback Preparedness

- [ ] **Database snapshot created**

- [ ] **Files directory backed up** (if relevant)

- [ ] **Feature toggle or revert plan documented**=

- [ ] **Rollback command tested locally or on staging**
  _Examples:_
  ```bash
  drush sql:drop -y && drush sql:cli < /backups/prod/YYYY-MM-DD--latest.sql
  rsync -avz backups/prod/files/ web/sites/default/files/
  drush cr
  ```

- [ ] **Deployment risks identified and documented**
  _e.g., New fields on existing content types, views changes, custom JS behavior_

      **Risks:**
      - Content type: `Fellowship`
      - View: `Application periods by status`
      - JS: `deadlineCountdown()`

- [ ] **Time-sensitive changes validated against real content**

---

### 🔄 Manual Rollback Steps (if needed)

If rollback is required, follow this order:
1. Restore database
2. Set code to previous tagged release
