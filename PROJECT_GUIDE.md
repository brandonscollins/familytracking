# Project Guide: Family Tracking Plugin

**Internal Documentation – For Maintainers & Contributors Only**

---

## Table of Contents
1. Overview
2. Goals & Vision
3. Functional Modes
4. Features & Ideas
5. Commonly Tracked Topics
6. Project Stages & Milestones
7. Task Tracking
8. Technical Notes
9. Change Log

---

## 1. Overview
A WordPress plugin to help families track, mediate, and resolve common disputes and shared responsibilities, especially among siblings. The plugin provides flexible modes for rotation, point tracking, random selection, and more.

## 2. Goals & Vision
- Provide fair, transparent, and customizable tracking for family tasks and privileges
- Support multiple dispute topics and rotation modes
- Enable easy administration and override
- Offer engaging, kid-friendly features (gamification, avatars)
- Ensure privacy and security for family data

## 3. Functional Modes
### Chronological Rotation
- Set topic/subject (e.g., "Dishes", "Front Seat")
- List participants & order (customizable)
- Set starting participant
- Choose interval: days, weeks, hours, months
- Option for custom time (e.g., Mon-Wed is A, Thu-Fri is B, etc.)
- Auto-advances on interval or at reset time
- Manual override/admin correction for missed turns, swaps, or exceptions

### Manual Tap-to-Advance
- Simple interface: tap/click to rotate to next participant
- Displays time and user when last advanced
- Optional admin/parent lock for approval-required advances
- Possible two-factor: confirmation pop-up to ensure honesty
- Log history ("who advanced," "when")

### Score/Point Tracking
- Tracks cumulative tasks or shares (e.g., chores)
- Option to reward completion, and settle disputes over fairness

### Random Picker
- For truly random turns (good for special privileges)
- Fair distribution ensured by tracking turn history

## 4. Features & Ideas
- History Log: See who did what, when; helps resolve disputes
- Skipped/Swapped Turns Handling: Easily skip or swap turns, including notes
- Multi-Topic Support: Manage many topics/rotations with one plugin
- Gamification: Award points, badges, streaks
- Visual Customization: Avatars/colors for participants
- API/Webhooks: Integrate with smart home devices
- Calendar Integration: Sync rotation with Google Calendar
- Statistics: Review fairness over time

## 5. Commonly Tracked Topics
- Who picks the movie/game/playlist
- Who rides "shotgun"/front seat
- Who gets the larger/last piece of food/dessert/snack
- Who gets to use a shared device (TV, gaming system, computer)
- Who chooses family activity/outfit/theme
- Who is first in the shower/bath
- Who feeds or walks the family pet
- Who gets control of the remote
- Who waters plants/takes out trash/other chores
- Turns for board/video games
- Who gets to invite a friend over next
- First to get help with homework
- Who gets to sit by the window/aisle (at home/car/travel)
- Who gives the pet their treat

## 6. Project Stages & Milestones
- [ ] Initial brainstorming & requirements gathering
- [ ] Core rotation logic implementation
- [ ] Admin interface & manual override
- [ ] History log & tracking
- [ ] Multi-topic support
- [ ] Gamification & customization features
- [ ] API/webhook integration
- [ ] Calendar sync
- [ ] Testing & QA
- [ ] Documentation & release

## 7. Task Tracking
| Task | Owner | Status | Notes |
|------|-------|--------|-------|
| Brainstorm features | | Completed | See section 4 & 5 |
| Implement rotation logic | | Completed | Rotation and manual advance implemented |
| Build admin UI | | Completed | Tracker CPT and meta box UI implemented |
| Score/Point tracking | | Completed | Points mode and updating implemented |
| Random picker | | Completed | Random mode implemented |
| Add history log | | Completed | Needed for audit trail |
| Multi-topic dashboard | | Completed | Trackers exist, but no grouped dashboard |
| Gamification | | Not Started | Badges, streaks, avatars |
| API/webhooks | | Not Started | |
| Calendar integration | | Not Started | |
| Write documentation | | Not Started | End-user docs pending |

## 8. Technical Notes
- Internal use only; do not expose or link to end users
- Update this file as features and requirements evolve
- Consider privacy, security, and scalability in all features

## 8a. Implementation Notes
- Plugin supports multiple trackers via CPT; grouped dashboard not yet implemented.
- Admin UI allows setup and editing of trackers and modes.
- AJAX handlers for advancing turns, updating points, and random picking are implemented.
- Public and admin JS/CSS loaded and functional.

## 8b. Known Issues or Limitations
- No history log/audit trail yet (cannot see who advanced, when).
- No advanced statistics or feedback form.
- No gamification or avatar support yet.
- No API/webhook or calendar integration.
## 9. Change Log
| Date | Change | Author |
|------|--------|--------|
| 2025-10-04 | Initial brainstorm and structure | brandonscollins |

## 9a. Next Steps
- Prioritize history log and multi-topic dashboard.
- Plan for gamification and feedback features after core tracking is stable.
- Implement automated testing for rotation and points logic.
- Write end-user documentation and update contributor guide.


## 10. User Stories & Personas
- **Parents:** Set up and manage rotations, approve changes, view history.
- **Kids:** Tap to advance turns, view whose turn it is, see points/badges.
- **Grandparents/Babysitters:** Temporary access for managing turns when parents are away.

**Example User Story:**
"As a parent, I want to quickly see who’s next for chores and override if needed, so I can keep things fair and organized."

## 11. Usage Context
- Designed for kiosk/touch screen setups.
- Prioritize large buttons, simple navigation, and minimal text.
- Accessibility: color contrast, readable fonts.

## 12. Requirements & Priorities
- **Must-Have:** Rotation logic, manual advance, history log, admin override.
- **Nice-to-Have:** Gamification, avatars, API/webhooks, calendar sync.

**Best Practice:** Start with core features, design for easy future expansion (modular code, clear APIs).

## 13. Platform/Browser Compatibility
- No special requirements, but test on common browsers (Chrome, Edge, Firefox) and WordPress versions.

## 14. Integration Points
- None needed for initial release.
- Leave hooks for future API/webhook/calendar integration.

## 15. Testing & QA Plan
- **Manual Testing:** Parents and kids use the kiosk to simulate real scenarios.
- **Automated Testing:** Unit tests for rotation logic and history tracking.
- **User Acceptance:** Get feedback from a few families before public release.

## 16. Deployment & Maintenance
- **Updates:** Managed via GitHub; document update process in the guide.
- **Backup:** Recommend regular WordPress backups before updates.

## 17. Documentation Plan
- **End-User:** Simple setup and usage instructions.
- **Contributor:** Internal guide (this file), code comments, and update notes.

## 18. Success Metrics
- **Feedback:** Add a simple feedback form for parents.
- **Usage:** Track number of rotations, overrides, and disputes resolved.

---


**End of Project Guide**
