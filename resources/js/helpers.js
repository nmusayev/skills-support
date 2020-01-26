let helpers = {
    loggedIn() {
      return auth.check
    },
    redirectIfNotLoggedId(router) {
        if(auth.check == 0) {
            router.push('/');
        }
    },
    checkUserIsQuestionOwner(question) {
        if(question !== null) {
            return auth.user.id === question.user.id;
        } else {
            return false;
        }
    },
    checIfUserAnswerOwner(answer) {
        return auth.user.id === answer.user.id;
    },
    checkUserVoted(vote_up_users) {
        return vote_up_users.includes(auth.user.id);
    }
};

export default helpers;
