    update `crm_ticket_mail` set `ctm_ticket_id` = 0, `ctm_status` = 'new', ctm_status_message = '';

    truncate crm_ticket;

    truncate crm_ticket_message;