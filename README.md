pyro-memberships
================

Generic membership handling for PyroCMS. Pyro already has groups, but it is intended for permission control and has no notion of periods. pyro-memberships instead introduces a more generic model, like so:

- profiles can be bound to any group (actually, any model).
- any profiles-to-group binding has a role attached to it (so, leader, member, etc)
- all bindings have a start/end history attached.
- if a profile ends its membership for a group and starts a new one with the same group later, a new memberships is created, preserving proper histories.

pyro-memberships is a splint of the now-derelict pyro-sports module. For this reason I'm building it primarily with pyro-sports and pyro-fees in mind, but I'll do so in a way that would allow other third-party modules to hook in and do their own thing with it, without messing with (or depending on) any of my other PyroCMS modules.
