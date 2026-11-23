# Smash Map × parry.gg: API suggestions

*This document was generated with Claude and proofread by me.*

Hi!

I've been working on the parry.gg integration for Smash Map, and I wanted to share a few notes from it. As a reminder, the map regularly imports upcoming Smash tournaments from start.gg, and it now pulls from parry.gg as well, through `TournamentService/GetTournaments` (JSON over gRPC-web with the `X-API-KEY` header).

The integration works and parry.gg tournaments are now on the map, but I had to work around a few things along the way. The points below are roughly ordered by how much they would help. None of them is urgent, so feel free to pick whatever makes sense on your side.

## 1. Filters on `GetTournaments`

This one would have the biggest impact. At the moment the endpoint returns every tournament, oldest first: 2,134 tournaments over 22 pages of 100. The first page alone is 100 completed tournaments, many of them tests. Only 62 tournaments are upcoming, so every sync downloads the full catalogue (about 60 seconds) and discards about 97 % of it.

Any of the following would help a lot:

- **Upcoming only**, e.g. `endDateAfter: now` or `states: [UPCOMING, IN_PROGRESS]`
- **By game**, e.g. `gameIds: [melee, ultimate, …]`
- **Changed since**, e.g. `updatedAfter: <last sync>`, for incremental syncs
- **Sorting by start date**
- **Online / in person**

With "upcoming", "game" and "changed since", each sync would only fetch what actually changed.

## 2. Unset dates sent as `1970-01-01`

When a date is not set, the API returns the protobuf default `1970-01-01T00:00:00Z`. Many tournaments have `endDate = 1970-01-01`: about 1,000 Smash events in one sync, including a few dozen upcoming ones that would otherwise have been dropped. Some events also have a 1970 `startDate`, with only the tournament holding the real one.

For now I treat a missing end date as a one-day event, and fall back to the tournament's start date when the event has none. It would be cleaner if unset dates were omitted (or sent as `null`). Requiring an end date or a duration when a tournament is published would be even better.

## 3. Identifying the main singles bracket

Smash Map only shows each game's main singles bracket, and there is currently no reliable way to identify it:

- `entrantSize` looks like the team size, but events named "Doubles" or "rock paper scissors doubles" still have `entrantSize: 1`.
- Nothing distinguishes the main bracket from side events such as amateur, redemption, ladder or randoms.

As a result, I infer it from event names with regular expressions (`singles`, `1v1`, `doubles`, `amateur`, …), and fall back to the biggest event that does not look like a side event. That becomes unreliable with names like "Scorching Singles" or "SMASH Tournament", and with main brackets simply named after the tournament ("bumfights 6", "Linking"). An enforced format field (`SINGLES` / `DOUBLES` / `TEAMS`) and a main/side event flag would remove the guesswork entirely.

## 4. Test tournaments in the public feed

The feed includes tournaments such as "my tournament", "test" or "Short Hopopop #22 TEST", and events named "rety" or "awesome event". I currently skip anything with "test" in its name (52 tournaments in the last sync) and anything whose `visibilityLevel` is not public, but this is guesswork. An `isTest` flag, or excluding test tournaments by default, would solve it.

## 5. In-person tournaments without an address

Some in-person tournaments have no `address` object, and their `venueAddress` is either empty or just `"."`. These cannot be placed on the map, so they are shown as "Location TBA". Requiring a geocoded address for in-person tournaments, at least before they appear in the API, would fix this.

Relatedly, could the possible `locationType` values be documented, along with whether the event-level value overrides the tournament-level one?

## 6. A `url` field

I build links from the `SLUG_TYPE_PRIMARY` slug (`https://parry.gg/{slug}`). My first attempt used a start.gg-style path (`/tournaments/{slug}/events/{event-slug}`), which returned a 404. A `url` field on tournaments (and possibly events) would avoid integrations having to construct URLs themselves.

## 7. Entrant counts

`entrantCount` is missing on about 12 % of events (3,428 of 3,883 had it), and `numAttendees` is missing on about 8 % of tournaments. Always sending `entrantCount` (0 when empty), plus a short explanation of how `entrantCount`, `entrantCap` and `numAttendees` differ, would help. Smash Map uses this count to notify users when an event they follow passes a size threshold.

## 8. Documentation and operational details

If documentation already exists and I missed it, a pointer would be great. Otherwise, it would be useful to know:

- The full message schemas and enum values
- The default and maximum `pageSize`, and whether cursors remain stable while data changes
- Rate limits and the polling interval you are comfortable with
- Whether image CDN URLs are permanent (I use `IMAGE_TYPE_PROFILE` first, then `IMAGE_TYPE_BANNER`)
- How versioning and breaking changes are handled

Longer term, a webhook or change feed (tournament created / updated / cancelled) would let me stop polling altogether.

## Summary

1. Filters (upcoming, game, changed since, sorting): by far the biggest improvement
2. Omit unset dates instead of sending 1970
3. A format field and a main/side flag on events
4. Exclude test tournaments from the feed
5. Require an address for in-person tournaments
6. Add a `url` field
7. Always send `entrantCount`
8. Documentation, rate limits, and possibly webhooks

I'm happy to test any of these against my importer whenever it suits you. Thanks a lot, both for considering these and for providing the API in the first place.
